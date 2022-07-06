<?php

use Illuminate\Support\Facades\Route;

if (!function_exists('flatArray')) {
    /**
     * Flatten array
     *
     * @param  array  $array
     *
     * @return array
     */
    function flatArray(array $array): array
    {
        $result = [];

        array_walk_recursive($array, function ($a) use (&$result) {
            $result[] = $a;
        });

        return $result;
    }
}

if (!function_exists('camelCaseToSnakeCase')) {
    function camelCaseToSnakeCase($str): string
    {
        return ltrim(
            strtolower(
                preg_replace(
                    '/[A-Z]([A-Z](?![a-z]))*/',
                    '_$0',
                    $str
                )
            ),
            '_'
        );
    }
}

if (!function_exists('getPermissionDataFromRoute')) {
    function getPermissionsFromRoute(): array
    {
        $appNamespace = app()->getNamespace();
        $routes = Route::getRoutes()->getRoutes();
        $data = [];

        foreach ($routes as $route) {
            $actionData = $route->getAction();

            /** Get only action has controller and controller namespace start with app namespace */
            if (array_key_exists('controller', $actionData)
                && str_starts_with($actionData['controller'], $appNamespace)) {
                $controllerAction = explode('@', $actionData['controller']);
                $controllerNameSplit = explode('\\', $controllerAction[0]);

                // Module
                $module = str_replace('Controller', '', end($controllerNameSplit));
                $module = camelCaseToSnakeCase($module);

                // Action
                $actionName = $controllerAction[1] ?? '';
                $actionName = camelCaseToSnakeCase($actionName);

                // Name
                $name = str_replace('_', ' ', $actionName.' '.$module);

                // Slug
                $slug = str_replace(' ', '_', $name);

                $data[] = [
                    'name' => $name,
                    'slug' => $slug,
                    'module' => $module,
                    'action_name' => $actionName,
                    'action' => $actionData['controller']
                ];
            }
        }

        return $data;
    }
}
