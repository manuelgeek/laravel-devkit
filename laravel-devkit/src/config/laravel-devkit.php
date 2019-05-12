<?php
/**
 * Created by PhpStorm.
 * User: marvincollins
 * Date: 1/1/19
 * Time: 2:34 PM
 */

return [
    'app-location' => 'app',
    'provider'  => 'ModuleServiceProvider',
    'namespace' => [
        'module' => 'App\\Modules\\',
        'crud' => 'App\\Http\\Controllers\\Admin\\',
    ],
    'locations' => [
        'modules' => app_path('Modules'),
        'crud' => 'app/Http/Controllers/Admin',
        'crud-config' => 'config/crud',
        'module-config' => 'config/module'
    ],
    'datetime' =>
        [
            'datetime_format' => 'd.m.Y H:m:s',
            'timezone' => 'Africa/Nairobi'
        ],

    'alab-login' => false, //als or laravel
];