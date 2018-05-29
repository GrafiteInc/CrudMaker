<?php

namespace Grafite\CrudMaker\Services;

class ConfigService
{
    /**
     * The app service.
     *
     * @var AppService
     */
    protected $appService;

    /**
     * CrudMaker Constructor.
     *
     * @param AppService $appService
     */
    public function __construct(AppService $appService)
    {
        $this->appService = $appService;
    }

    /**
     * Generate the basic config
     *
     * @param  string $framework
     * @param  string $appPath
     * @param  string $basePath
     * @param  string $appNamespace
     * @param  string $table
     * @param  array $options
     *
     * @return array
     */
    public function basicConfig($framework, $appPath, $basePath, $appNamespace, $table, $options)
    {
        $config = [
            'framework'                  => $framework,
            'bootstrap'                  => false,
            'semantic'                   => false,
            'template_source'            => '',
            '_sectionPrefix_'            => '',
            '_sectionTablePrefix_'       => '',
            '_sectionRoutePrefix_'       => '',
            '_sectionNamespace_'         => '',
            '_path_facade_'              => $appPath.'/Facades',
            '_path_service_'             => $appPath.'/Services',
            '_path_model_'               => $appPath.'/Models',
            '_path_controller_'          => $appPath.'/Http/Controllers/',
            '_path_api_controller_'      => $appPath.'/Http/Controllers/Api',
            '_path_views_'               => $basePath.'/resources/views',
            '_path_tests_'               => $basePath.'/tests',
            '_path_request_'             => $appPath.'/Http/Requests/',
            '_path_routes_'              => $basePath.'/routes/web.php',
            '_path_api_routes_'          => $basePath.'/routes/api.php',
            '_path_migrations_'          => $basePath.'/database/migrations',
            '_path_factory_'             => $basePath.'/database/factories/'.snake_case($table).'Factory.php',
            'routes_prefix'              => '',
            'routes_suffix'              => '',
            '_app_namespace_'            => 'App\\',
            '_namespace_services_'       => $appNamespace.'Services',
            '_namespace_facade_'         => $appNamespace.'Facades',
            '_namespace_model_'          => $appNamespace.'Models',
            '_namespace_controller_'     => $appNamespace.'Http\Controllers',
            '_namespace_api_controller_' => $appNamespace.'Http\Controllers\Api',
            '_namespace_request_'        => $appNamespace.'Http\Requests',
            '_table_name_'               => str_plural(strtolower(snake_case($table))),
            '_lower_case_'               => strtolower(snake_case($table)),
            '_lower_casePlural_'         => str_plural(strtolower(snake_case($table))),
            '_camel_case_'               => ucfirst(camel_case($table)),
            '_camel_casePlural_'         => str_plural(camel_case($table)),
            '_ucCamel_casePlural_'       => ucfirst(str_plural(camel_case($table))),
            '_plain_space_textLower_'    => strtolower(str_replace('_', ' ', snake_case($table))),
            '_plain_space_textFirst_'    => ucfirst(strtolower(str_replace('_', ' ', snake_case($table)))),
            '_snake_case_'               => snake_case($table),
            '_snake_casePlural_'         => str_plural(snake_case($table)),
            'options-api'                => $options['api'],
            'options-apiOnly'            => $options['apiOnly'],
            'options-ui'                 => $options['ui'],
            'options-serviceOnly'        => $options['serviceOnly'],
            'options-withFacade'         => $options['withFacade'],
            'options-withBaseService'    => $options['withBaseService'],
            'options-migration'          => $options['migration'],
            'options-schema'             => $options['schema'],
            'options-relationships'      => $options['relationships'],
        ];

        return $config;
    }

    /**
     * Set the config of the CRUD.
     *
     * @param array  $config
     * @param string $section
     * @param string $table
     * @param array  $splitTable
     *
     * @return array
     */
    public function configASectionedCRUD($config, $section, $table, $splitTable)
    {
        $appPath = app()->path();
        $basePath = app()->basePath();
        $appNamespace = $this->appService->getAppNamespace();

        $sectionalConfig = [
            '_sectionPrefix_'            => strtolower($section).'.',
            '_sectionTablePrefix_'       => strtolower($section).'_',
            '_sectionRoutePrefix_'       => strtolower($section).'/',
            '_sectionNamespace_'         => ucfirst($section).'\\',
            '_path_facade_'              => $appPath.'/Facades',
            '_path_service_'             => $appPath.'/Services',
            '_path_model_'               => $appPath.'/Models/'.ucfirst($section).'/'.ucfirst($table),
            '_path_controller_'          => $appPath.'/Http/Controllers/'.ucfirst($section).'/',
            '_path_api_controller_'      => $appPath.'/Http/Controllers/Api/'.ucfirst($section).'/',
            '_path_views_'               => $basePath.'/resources/views/'.strtolower($section),
            '_path_tests_'               => $basePath.'/tests',
            '_path_request_'             => $appPath.'/Http/Requests/'.ucfirst($section),
            '_path_routes_'              => $appPath.'/Http/routes.php',
            '_path_api_routes_'          => $appPath.'/Http/api-routes.php',
            '_path_migrations_'          => $basePath.'/database/migrations',
            '_path_factory_'             => $basePath.'/database/factories/'.snake_case($table).'Factory.php',
            'routes_prefix'              => "\n\nRoute::group(['namespace' => '".ucfirst($section)."', 'prefix' => '".strtolower($section)."', 'middleware' => ['web']], function () { \n",
            'routes_suffix'              => "\n});",
            '_app_namespace_'            => $appNamespace,
            '_namespace_services_'       => $appNamespace.'Services\\'.ucfirst($section),
            '_namespace_facade_'         => $appNamespace.'Facades',
            '_namespace_model_'          => $appNamespace.'Models\\'.ucfirst($section).'\\'.ucfirst($table),
            '_namespace_controller_'     => $appNamespace.'Http\Controllers\\'.ucfirst($section),
            '_namespace_api_controller_' => $appNamespace.'Http\Controllers\Api\\'.ucfirst($section),
            '_namespace_request_'        => $appNamespace.'Http\Requests\\'.ucfirst($section),
            '_lower_case_'               => strtolower($splitTable[1]),
            '_lower_casePlural_'         => str_plural(strtolower($splitTable[1])),
            '_camel_case_'               => ucfirst(camel_case($splitTable[1])),
            '_camel_casePlural_'         => str_plural(camel_case($splitTable[1])),
            '_ucCamel_casePlural_'       => ucfirst(str_plural(camel_case($splitTable[1]))),
            '_table_name_'               => str_plural(strtolower(implode('_', $splitTable))),
        ];

        $config = array_merge($config, $sectionalConfig);
        $config = array_merge($config, app('config')->get('crudmaker.sectioned', []));
        $config = $this->setConfig($config, $section, $table);

        $pathsToMake = [
            '_path_model_',
            '_path_controller_',
            '_path_api_controller_',
            '_path_views_',
            '_path_request_',
        ];

        foreach ($config as $key => $value) {
            if (in_array($key, $pathsToMake) && !file_exists($value)) {
                mkdir($value, 0777, true);
            }
        }

        return $config;
    }

    /**
     * Get the templates directory.
     *
     * @param string $framework
     *
     * @return string
     */
    public function getTemplateConfig($framework)
    {
        $templates = __DIR__.'/../Templates/'.$framework;

        $templates = app('config')->get('crudmaker.template_source', $templates);

        return $templates;
    }

    /**
     * Set the config.
     *
     * @param array  $config
     * @param string $section
     * @param string $table
     *
     * @return array
     */
    public function setConfig($config, $section, $table)
    {
        if (!empty($section)) {
            foreach ($config as $key => $value) {
                $config[$key] = str_replace('_table_', ucfirst($table), str_replace('_section_', ucfirst($section), str_replace('_sectionLowerCase_', strtolower($section), $value)));
            }
        } else {
            foreach ($config as $key => $value) {
                $config[$key] = str_replace('_table_', ucfirst($table), $value);
            }
        }

        return $config;
    }
}
