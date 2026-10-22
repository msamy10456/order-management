<?php

namespace Modules\Auth\Http\Requests;

use App\Helpers\RequestHelper;
use App\Traits\HttpResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class CodeSendRequest extends FormRequest
{
    use HttpResponse;

    // public function prepareForValidation(): void
    // {
    //     RequestHelper::formatPhoneNumber($this, 'handle');
    // }

    public function rules(): array
    {
        return [
            'handle' => ['required'],
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $this->throwValidationException($validator);
    }
}
