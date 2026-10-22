<?php

namespace Modules\Payment\Http\Requests;

use App\Helpers\ValidationRuleHelper;
use App\Traits\HttpResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Modules\Payment\Enum\PaymentMethodEnum;
use Modules\Payment\Enum\PaymentStatusEnum;

class PaymentRequest extends FormRequest
{
    use HttpResponse;

    public function rules(): array
    {
        $inUpdate = ! preg_match('/.*payment$/', $this->url());

        return [
            'order_id' => ValidationRuleHelper::foreignKeyRules([
                'required' => $inUpdate ? 'sometimes' : 'required',
                'exists' => 'exists:orders,id',
            ]),
            'status' => ValidationRuleHelper::enumRules(PaymentStatusEnum::availableTypes(), [
                'required' => $inUpdate ? 'sometimes' : 'required',
            ]),
            'method' => ValidationRuleHelper::enumRules(PaymentMethodEnum::availableTypes(), [
                'required' => $inUpdate ? 'sometimes' : 'required',
            ]),
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
