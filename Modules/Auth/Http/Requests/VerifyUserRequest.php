<?php

namespace Modules\Auth\Http\Requests;

use App\Traits\HttpResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class VerifyUserRequest extends FormRequest
{
    use HttpResponse;

    // public function prepareForValidation()
    // {
    //     request()->input('type') == UserTypeEnum::VENDOR ??
    //         RequestHelper::formatPhoneNumber($this, 'handle');
    // }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'handle' => ['required'],
            'code' => ['required', 'numeric'],
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $this->throwValidationException($validator);
    }
}
