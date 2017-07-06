<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/3
 * Time: 16:02
 */

namespace Louis\CrudMaker\Services;
use Config;

class ConfigService
{
    public function __construct()
    {
    }

    public function getConfig($table, $options){
        $appPath = app()->path();
        $basePath = app()->basePath();
        $appNamespace = app()->getNamespace();

        $config = [
            'template_path'             => $basePath.'/resources/crud/',

            '_path_service_'            => $appPath.'/Services/',
            '_path_model_'              => $appPath.'/Models/',
            '_path_controller_'        => $appPath.'/Http/Controllers/',
            '_path_api_controller_'    => $appPath.'/Http/Controllers/Api/',
            '_path_request_'            => $appPath.'/Http/Requests/',
            '_path_views_'              => $basePath.'/resources/views/',
            '_file_routes_'             => $basePath.'/routes/web.php',
            '_file_api_routes_'        => $basePath.'/routes/api.php',

            '_routes_prefix_'              => '',

            '_path_migrations_'          => $basePath.'/database/migrations/',

            '_app_namespace_'            => $appNamespace,
            '_namespace_services_'       => $appNamespace.'Services',
            '_namespace_model_'          => $appNamespace.'Models',
            '_namespace_controller_'     => $appNamespace.'Http\Controllers',
            '_namespace_api_controller_' => $appNamespace.'Http\Controllers\Api',
            '_namespace_request_'         => $appNamespace.'Http\Requests',

            '_table_name_'                => strtolower(snake_case($table)),
            '_lower_case_'                => strtolower(snake_case($table)),
            '_lower_casePlural_'         => str_plural(strtolower(snake_case($table))),
            '_camel_case_'                => camel_case($table),
            '_ucCamel_case_'              => ucfirst(camel_case($table)),
            '_camel_casePlural_'         => str_plural(camel_case($table)),
            '_ucCamel_casePlural_'       => ucfirst(str_plural(camel_case($table))),
            '_snake_case_'                => snake_case($table),
            '_snake_casePlural_'         => str_plural(snake_case($table)),

            '_plain_space_textLower_'    => strtolower(str_replace('_', ' ', snake_case($table))),
            '_plain_space_textFirst_'    => ucfirst(strtolower(str_replace('_', ' ', snake_case($table)))),

            'options-api'                => $options['api'],
            'options-apiOnly'            => $options['apiOnly'],
            'options-serviceOnly'       => $options['serviceOnly'],
            'options-migration'         => $options['migration'],
            'options-schema'             => $options['schema'],
            'options-relationships'     => $options['relationships'],
        ];

        $config = array_merge($config, Config::get('crud', []));
        foreach ($config as $key=>$value){
            if (substr($key, 0, 6)== "_path_" && !is_dir($value)){
                mkdir($value, 0777, true);
            }
        }

        return $config;

    }
}