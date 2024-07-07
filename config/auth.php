<?php

return [

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'employees',
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'employees', // Use 'employees' provider for web guard
        ],

        'api' => [
            'driver' => 'token',
            'provider' => 'employees', // Use 'employees' provider for api guard
            'hash' => false,
        ],

        'admin' => [
            'driver' => 'session', // Use session driver for admin guard
            'provider' => 'admins', // Use 'admins' provider for admin guard
        ],
    ],

    'providers' => [
        'employees' => [
            'driver' => 'eloquent',
            'model' => App\Models\Employee::class,
        ],

        'admins' => [
            'driver' => 'eloquent',
            'model' => App\Models\Admin::class,
        ],
    ],

    'passwords' => [
        'employees' => [
            'provider' => 'employees', // Use 'employees' provider for employee passwords
            'table' => 'password_resets',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,

];
