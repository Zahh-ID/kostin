<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if ($user === null) {
            abort(Response::HTTP_UNAUTHORIZED, 'Unauthenticated.');
        }

        if (empty($roles)) {
            return $next($request);
        }

        if (! in_array($user->role, $roles, true)) {
            abort(Response::HTTP_FORBIDDEN, 'Forbidden.');
        }

        return $next($request);
    }
}
