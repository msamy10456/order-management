<?php

namespace Modules\Auth\Http\Controllers;

use App\Traits\HttpResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Auth\Actions\Register\ClientUserRegister;
use Modules\Auth\Http\Requests\Register\ClientUserRegisterRequest;

class RegisterController extends Controller
{
    use HttpResponse;

    public function user(ClientUserRegisterRequest $request, ClientUserRegister $normalUserRegister): JsonResponse
    {
        $normalUserRegister->handle($request->validated());

        return $this->createdResponse(
            message: translate_success_message('user', 'created')
        );
    }
}
