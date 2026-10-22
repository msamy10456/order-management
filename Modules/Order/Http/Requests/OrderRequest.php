<?php

namespace Modules\Order\Http\Requests;

use App\Helpers\ValidationRuleHelper;
use App\Traits\HttpResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Modules\Order\Enum\OrderStatusEnum;

class OrderRequest extends FormRequest
{
    use HttpResponse;

    protected function prepareForValidation(): void
    {
        if ($this->has('status') && is_string($this->status)) {
            $parsed = OrderStatusEnum::parseStatus($this->status);
            if ($parsed !== null) {
                $this->merge(['status' => $parsed]);
            }
        }
    }

    public function rules(): array
    {
        $inUpdate = ! preg_match('/.*order$/', $this->url());

        return [
            'customer_name' => ValidationRuleHelper::stringRules([
                'required' => $inUpdate ? 'sometimes' : 'required',
            ]),
            'customer_email' => ValidationRuleHelper::emailRules([
                'required' => 'nullable',
            ]),
            'customer_phone' => ValidationRuleHelper::phoneRules([
                'required' => 'nullable',
            ]),
            'address' => ValidationRuleHelper::stringRules([
                'required' => 'nullable',
            ]),
            'status' => ValidationRuleHelper::enumRules(OrderStatusEnum::availableTypes(), [
                'required' => 'sometimes',
            ]),
            'items' => ValidationRuleHelper::arrayRules([
                'required' => $inUpdate ? 'sometimes' : 'required',
            ]),
            'items.*.product_name' => ValidationRuleHelper::stringRules([
                'required' => $inUpdate ? 'sometimes' : 'required',
            ]),
            'items.*.quantity' => ValidationRuleHelper::integerRules([
                'required' => $inUpdate ? 'sometimes' : 'required',
                'min' => 'min:1',
            ]),
            'items.*.price' => ValidationRuleHelper::doubleRules([
                'required' => $inUpdate ? 'sometimes' : 'required',
                'min' => 'min:0',
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
