<?php

namespace App\Console\Commands;

use App\Models\Permission;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

class GeneratePermission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command generate permission';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function generatePermissionFromRoute()
    {
        $appNamespace = app()->getNamespace();
        $routes = Route::getRoutes()->getRoutes();
        $data = [];

        foreach ($routes as $route) {
            $actionData = $route->getAction();

            if (array_key_exists('controller', $actionData)
                && str_starts_with($actionData['controller'], $appNamespace)) {
                $controllerAction = explode('@', $actionData['controller']);
                $controllerNameSplit = explode('\\', $controllerAction[0]);

                // Module
                $module = str_replace('Controller', '', end($controllerNameSplit));
                $module = ltrim(
                    strtolower(
                        preg_replace(
                            '/[A-Z]([A-Z](?![a-z]))*/',
                            ' $0',
                            $module
                        )
                    ),
                    ' '
                );

                // Action
                $actionName = $controllerAction[1] ?? '';
                $actionName = ltrim(
                    strtolower(
                        preg_replace(
                            '/[A-Z]([A-Z](?![a-z]))*/',
                            ' $0',
                            $actionName
                        )
                    ),
                    ' '
                );

                // Slug
                $name = $actionName . ' ' . $module;

                // Name
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

        Permission::upsert($data, ['slug'], ['slug']);
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        try {
            $this->generatePermissionFromRoute();

            return 1;
        } catch (\Exception $e) {
            return 0;
        }
    }
}
