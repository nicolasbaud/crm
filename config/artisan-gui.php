<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Middleware list for web routes
    |--------------------------------------------------------------------------
    |
    | You can pass any middleware for routes, by default it's just [web] group
    | of middleware.
    |
    */
    'middlewares' => [
        'web',
        //        'auth'
    ],

    /*
    |--------------------------------------------------------------------------
    | Route prefix
    |--------------------------------------------------------------------------
    |
    | Prefix for gui routes. By default url is [/~artisan-gui].
    | For your wish you can set it for example 'my-'. So url will be [/my-artisan-gui].
    |
    | Why tilda? It's selected for prevent route names correlation.
    |
    */
    'prefix' => 'admin/',

    /*
    |--------------------------------------------------------------------------
    | Home url
    |--------------------------------------------------------------------------
    |
    | Where to go when [home] button is pressed
    |
    */
    'home' => '/',

    /*
    |--------------------------------------------------------------------------
    | Only on local
    |--------------------------------------------------------------------------
    |
    | Flag that preventing showing commands if environment is on production
    |
    */
    'local' => true,

    /*
    |--------------------------------------------------------------------------
    | List of command permissions
    |--------------------------------------------------------------------------
    |
    | Specify permissions to every single command. Can be a string or array
    | of permissions
    |
    | Example:
    |   'make:controller' => 'create-controller',
    |   'make:event' => ['generate-files', 'create-event'],
    |
    */
    'permissions' => [],

    /*
    |--------------------------------------------------------------------------
    | List of commands
    |--------------------------------------------------------------------------
    |
    | List of all default commands that has end of execution. Commands like
    | [serve] not supported in case of server side behavior of php.
    | Keys means group. You can shuffle commands as you wish and add your own.
    |
    */
    'commands' => [
        'make' => [
            'make:filament-user',
        ]
    ]

];
