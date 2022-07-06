<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CheckHasAllPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  mixed    ...$permissions
     *
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$permissions)
    {
        $permissions = empty($permissions) ? [null] : $permissions;

        if (!auth()->user()->hasAllPermission($permissions)) {
            abort(403);
        }

        return $next($request);
    }
}
