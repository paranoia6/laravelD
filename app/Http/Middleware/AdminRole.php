<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403);
        }

        $role = $user->role;

        if ($role instanceof \BackedEnum) {
            $role = (string) $role->value;
        } elseif (is_string($role)) {
            $role = trim($role);
        } elseif (method_exists($user, 'roleValue')) {
            $role = $user->roleValue();
        } else {
            $role = null;
        }

        if (! is_string($role) || ! in_array($role, $roles, true)) {
            abort(403);
        }

        return $next($request);
    }
}
