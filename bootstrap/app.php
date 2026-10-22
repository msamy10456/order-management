<?php

use App\Exceptions\InternalServerErrorException;
use App\Exceptions\ValidationErrorsException;
use App\Http\Middleware\AccountMustBeActive;
use App\Http\Middleware\AlwaysAcceptJson;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\MustBeVerified;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\SetDefaultLocale;
use App\Http\Middleware\TrustProxies;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance;
use Illuminate\Foundation\Http\Middleware\TrimStrings;
use Illuminate\Foundation\Http\Middleware\ValidatePostSize;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Middleware\HandleCors;
use Illuminate\Support\Str;
use Modules\Auth\Helpers\AuthExceptionHelper;
use Modules\Auth\Http\Middleware\CheckUserType;
use Modules\Role\Helpers\PermissionExceptionHelper;
use Modules\Role\Http\Middleware\PermissionMiddleware;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

if (! function_exists('handleExceptions')) {

    function handleExceptions(Exceptions $exceptions): void
    {
        $httpResponse = new class
        {
            use \App\Traits\HttpResponse;
        };

        AuthExceptionHelper::handle($exceptions);
        PermissionExceptionHelper::handle($exceptions);

        $exceptions->renderable(function (ValidationErrorsException $e) use ($httpResponse) {
            return $httpResponse->validationErrorsResponse($e->getErrors());
        });

        $exceptions->renderable(function (InternalServerErrorException $e) use ($httpResponse) {
            return $httpResponse->errorResponse(message: $e->getMessage());
        });

        $exceptions->renderable(function (NotFoundHttpException $e, $request) use ($httpResponse) {
            $msg = Str::contains($e->getMessage(), 'No query', true)
                ? translate_word('record_not_found')
                : $e->getMessage();

            return $httpResponse->errorResponse(null, Response::HTTP_NOT_FOUND, $msg);
        });

        $exceptions->renderable(function (MethodNotAllowedHttpException $e, $request) use ($httpResponse) {
            return $httpResponse->errorResponse(null, Response::HTTP_METHOD_NOT_ALLOWED, $e->getMessage());
        });

        $exceptions->renderable(function (ThrottleRequestsException $e, $request) use ($httpResponse) {
            return $httpResponse->errorResponse(null, Response::HTTP_TOO_MANY_REQUESTS, translate_word('rate_limit_exceeded'));
        });

        $exceptions->renderable(function (HttpException $e) use ($httpResponse) {
            $message = $e->getMessage() === 'Service Unavailable'
                ? translate_word('maintenance_mode')
                : $e->getMessage();

            return $httpResponse->errorResponse(code: $e->getStatusCode(), message: $message);
        });
    }
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up'
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append([
            SetDefaultLocale::class,
            AlwaysAcceptJson::class,
        ]);

        $middleware->api(['throttle:api']);

        $middleware->use([
            TrustProxies::class,
            HandleCors::class,
            PreventRequestsDuringMaintenance::class,
            ValidatePostSize::class,
            TrimStrings::class,
            ConvertEmptyStringsToNull::class,
        ]);

        $middleware->alias([
            'auth' => Authenticate::class,
            'guest' => RedirectIfAuthenticated::class,
            'permission' => PermissionMiddleware::class,
            'account_must_be_active' => AccountMustBeActive::class,
            'must_be_verified' => MustBeVerified::class,
            'user_type_in' => CheckUserType::class,
        ]);
    })
    ->withExceptions('handleExceptions')
    ->create();
