<?php

return [
    // Is check route permission
    'check_route_permission' => true,

    // Path and route do not check
    'excepts' => [
        'paths' => [
            'login'
        ],
        'routes' => [
            'auth.login'
        ]
    ],

    // Seeding
    'super_admin' => [
        'name' => 'Super Admin',
        'slug' => 'super_admin',
        'description' => 'super admin'
    ]
];
