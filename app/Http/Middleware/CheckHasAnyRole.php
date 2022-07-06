<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CheckHasAnyRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  mixed    ...$roles
     *
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
//        dd('here', auth()->user(), $roles, !auth()->user()->hasAnyRole($roles));
        $roles = empty($roles) ? [null] : $roles;

        if (!auth()->user()->hasAnyRole($roles)) {
            abort(403);
        }

        return $next($request);
    }
}
