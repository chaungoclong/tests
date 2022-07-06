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
     * @param  Request  $request
     * @param  Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     *
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        /** Check authenticated */
        if (!$user) {
            return abort(401);
        }

        /** Pass if config not check permission */
        if (!config('permissions.check_route_permission', true)) {
            return $next($request);
        }

        /** Pass if user is super admin or request is always allowed */
        if ($user->isSuperAdmin() || $this->checkRequestIsAlwaysAllowed($request)) {
            return $next($request);
        }

        /** Check route permission */
        if (!$this->checkRoutePermission($request)) {
            return abort(403);
        }

        return $next($request);
    }

    /**
     * Check route permission
     *
     * @param $request
     *
     * @return bool
     */
    protected function checkRoutePermission($request): bool
    {
        $action = Route::getCurrentRoute()->getAction();
        $action = $action['controller'] ?? null;

        /** Pass if action of current route not have controller(e.g: Closure) */
        if ($action === null) {
            return true;
        }

        return auth()->user()->hasAction($action);
    }

    /**
     * Check request is always allowed
     *
     * @param $request
     *
     * @return bool
     */
    protected function checkRequestIsAlwaysAllowed($request): bool
    {
        $exceptPaths = config('permissions.excepts.paths', []);
        $exceptRoutes = config('permissions.excepts.routes', []);

        /** Do not check these paths */
        foreach ($exceptPaths as $path) {
            $path = ($path !== '/') ? trim($path, '/') : $path;

            if ($request->is($path)) {
                return true;
            }
        }

        /** Do not check these routes */
        foreach ($exceptRoutes as $route) {
            $route = trim($route, '.');

            if ($request->routeIs($route)) {
                return true;
            }
        }

        return false;
    }
}
