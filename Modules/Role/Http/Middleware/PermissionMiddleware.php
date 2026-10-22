<?php

namespace Modules\Role\Http\Middleware;

use App\Traits\HttpResponse;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Modules\Role\Exceptions\UserPermissionException;

class PermissionMiddleware
{
    use HttpResponse;

    /**
     * Handle an incoming request.
     *
     * @throws AuthenticationException
     * @throws UserPermissionException
     */
    public function handle(Request $request, Closure $next, string $permissionName)
    {
        if (! auth()->check()) {
            $this->throwNotAuthenticated();
        }

        if (! auth()->user()->hasPermissionTo($permissionName)) {
            UserPermissionException::notExists();
        }

        return $next($request);
    }
}
