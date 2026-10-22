<?php

namespace Modules\Payment\Http\Requests;

use App\Helpers\ValidationRuleHelper;
use App\Traits\HttpResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Modules\Payment\Gateways\PaymentGatewayManager;

class ProcessPaymentRequest extends FormRequest
{
    use HttpResponse;

    public function rules(): array
    {
        $supportedGateways = (new PaymentGatewayManager)->availableGateways();

        return [
            'order_id' => ValidationRuleHelper::foreignKeyRules([
                'required' => 'required',
                'exists' => 'exists:orders,id',
            ]),
            'method' => [
                'required',
                'string',
                Rule::in($supportedGateways),
            ],
            'card_number' => ['nullable', 'string'],
            'simulate_failure' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @throws ValidationException
     */
    public function failedValidation(Validator $validator): void
    {
        $this->throwValidationException($validator);
    }
}
