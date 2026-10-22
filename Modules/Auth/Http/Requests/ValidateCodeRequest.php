<?php

namespace Modules\Auth\Http\Requests;

use App\Helpers\ValidationRuleHelper;
use App\Traits\HttpResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Modules\Auth\Enums\VerifyTokenTypeEnum;

class ValidateCodeRequest extends FormRequest
{
    use HttpResponse;

    // public function prepareForValidation()
    // {
    //     RequestHelper::formatPhoneNumber($this, 'handle');
    // }

    public function rules(): array
    {
        return [
            'code' => ValidationRuleHelper::integerRules(),
            'code_type' => ValidationRuleHelper::enumRules(VerifyTokenTypeEnum::availableTypes()),
            'handle' => ['required'],
        ];
    }

    public function failedValidation(Validator $validator): void
    {
        $this->throwValidationException($validator);
    }
}
