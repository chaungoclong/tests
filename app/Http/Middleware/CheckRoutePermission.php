<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

class CheckRoutePermission
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     *
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        auth()->loginUsingId(1);

        if(!auth()->check()) {
            return abort(401);
        }

        if(!$this->checkRoutePermission($request)) {
            return abort(403);
        }

        return $next($request);
    }

    protected function checkRoutePermission($request): bool
    {
        $user = auth()->user();

        if($user->isSuperAdmin()) {
            return true;
        }

        /** Do not check */
        $exceptPaths = config('permissions.excepts.paths', []);
        $exceptRoutes = config('permissions.excepts.routes', []);

        foreach ($exceptPaths as $path) {
            $path = ($path !== '/') ? trim($path, '/') : $path;

            if($request->is($path)) {
                return true;
            }
        }

        foreach ($exceptRoutes as $route) {
            $route = trim($route, '.');

            if($request->routeIs($route)) {
                return true;
            }
        }

        /** Check route permissions */
        $action = Route::getCurrentRoute()->getAction();
        $action = $action['controller'] ?? null;

        if($action === null) {
            return true;
        }

        return $user->hasAction($action);
    }
}
