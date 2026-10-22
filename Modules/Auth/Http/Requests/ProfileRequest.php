<?php

namespace Modules\Auth\Http\Requests;

use App\Helpers\ValidationRuleHelper;
use App\Traits\HttpResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Modules\Auth\Enums\GenderEnum;
use Modules\Auth\Enums\UserTypeEnum;
use Modules\Auth\Helpers\UserTypeHelper;

class ProfileRequest extends FormRequest
{
    use HttpResponse;

    public function rules(): array
    {
        $inMobileTypes = in_array(UserTypeEnum::getUserType(), UserTypeHelper::mobileTypes());

        return [
            'name' => ValidationRuleHelper::stringRules(['required' => 'sometimes']),
            'phone' => ValidationRuleHelper::phoneRules(['required' => 'sometimes']),
            'email' => ValidationRuleHelper::emailRules(['required' => $inMobileTypes ? 'nullable' : 'sometimes']),
            'gender' => ValidationRuleHelper::enumRules( GenderEnum::getValues(), ['required' => 'nullable']),
            'avatar' => ValidationRuleHelper::storeOrUpdateImageRules(true),
            'address' => ValidationRuleHelper::stringRules(['required' => 'sometimes']),
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
