<?php

namespace Modules\Auth\Http\Requests\Register;

use App\Helpers\RequestHelper;
use App\Helpers\ValidationRuleHelper;
use App\Traits\HttpResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class EmployeeUserRegisterRequest extends FormRequest
{
    use HttpResponse;

    public function prepareForValidation(): void
    {
        RequestHelper::formatPhoneNumber($this);
    }

    public function rules(): array
    {
        return [
            ...BaseRegisterRequest::baseRules(),
            'avatar' => ValidationRuleHelper::fileRules(true),
            'role_id' => ValidationRuleHelper::foreignKeyRules([
                'exists' => 'exists:roles,id',
            ]),
        ];
    }

    public function failedValidation(Validator $validator): void
    {
        $this->throwValidationException($validator);
    }

    public function messages(): array
    {
        return [
            'exists' => __('validation.not_exists'),
        ];
    }
}
