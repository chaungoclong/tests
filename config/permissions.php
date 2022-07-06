<?php

return [
    // Is check route permission
    'check_route_permission' => true,

    // Models
    'models' => [
        'permission' => \App\Models\Permission::class,
        'role' => \App\Models\Role::class
    ],

    // Tables
    'tables' => [
        'roles' => 'roles',
        'permissions' => 'permissions',
        'permission_role' => 'permission_role',
        'role_user' => 'role_user'
    ],

    // Path and route do not check
    'excepts' => [
        'paths' => [
            'login'
        ],
        'routes' => [
            'auth.login'
        ]
    ],

    // Super admin
    'super_admin' => [
        'name' => 'Super Admin',
        'slug' => 'super_admin',
        'description' => 'super admin'
    ]
];
