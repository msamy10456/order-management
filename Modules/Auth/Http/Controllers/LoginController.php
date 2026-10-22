<?php

namespace Modules\Auth\Http\Controllers;

use App\Traits\HttpResponse;
use Illuminate\Routing\Controller;
use Modules\Auth\Http\Requests\Login\DashboardLoginRequest;
use Modules\Auth\Http\Requests\Login\LoginRequest;
use Modules\Auth\Http\Requests\Login\MobileLoginRequest;
use Modules\Auth\Services\LoginService;
use Modules\Auth\Transformers\UserResource;

class LoginController extends Controller
{
    use HttpResponse;

    public function __construct(private readonly LoginService $loginService) {}

    public function mobile(DashboardLoginRequest $request)
    {
        $user = $this->loginService->Mobile($request->validated());

        return $this->okResponse(UserResource::make($user), message: translate_word('logged_in'));
    }

    public function dashboard(DashboardLoginRequest $request)
    {
        $data = $request->validated();

        $user = $this->loginService->dashboard($data);

        return $this->okResponse(UserResource::make($user), message: translate_word('logged'));
    }
}
