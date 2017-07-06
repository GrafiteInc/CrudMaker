<?php

/*
|--------------------------------------------------------------------------
| CrudMaker Config
|--------------------------------------------------------------------------
|
| WARNING! do not change any thing that starts and ends with _
|
*/

return [
    "template_path"              => app()->basePath().'/resources/crud/',

    '_path_service_'             => app()->path().'/Services/',
    '_path_model_'               => app()->path().'/Models/',
    '_path_controller_'          => app()->path().'/Http/Controllers/',
    '_path_api_controller_'     =>app()->path().'/Http/Controllers/Api/',
    '_path_views_'               => app()->basePath().'/resources/views/',

    '_path_request_'             => app()->path().'/Http/Requests/',
    '_file_routes_'              => app()->basePath().'/routes/web.php',
    '_file_api_routes_'          => app()->basePath().'/routes/api.php',

    '_routes_prefix_'              => '',

    '_app_namespace_'            => app()->getNamespace(),
    '_namespace_services_'       => app()->getNamespace().'Services',
    '_namespace_model_'          => app()->getNamespace().'Models',
    '_namespace_controller_'     => app()->getNamespace().'Http\Controllers',
    '_namespace_api_controller_' => app()->getNamespace().'Http\Controllers\Api',
    '_namespace_request_'        => app()->getNamespace().'Http\Requests',
];
