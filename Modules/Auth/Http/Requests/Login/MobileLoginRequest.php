<?php

namespace Modules\Auth\Http\Requests\Login;

use App\Helpers\RequestHelper;
use App\Helpers\ValidationRuleHelper;
use App\Traits\HttpResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Modules\Auth\Helpers\UserTypeHelper;

class MobileLoginRequest extends FormRequest
{
    use HttpResponse;

    public function prepareForValidation()
    {
        RequestHelper::formatPhoneNumber($this);
    }

    public function rules(): array
    {
        return [
            'type' => ValidationRuleHelper::enumRules(UserTypeHelper::mobileTypes()),
            'phone' => ValidationRuleHelper::phoneRules(),
            'password' => 'required',
            'fcm_token' => ['sometimes', 'string'],
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
