<?php

use org\bovigo\vfs\vfsStream;
use Yab\CrudMaker\Services\ConfigService;

class ConfigServiceTest extends TestCase
{
    protected $service;
    protected $config;

    public function setUp()
    {
        parent::setUp();
        $this->service = app(ConfigService::class);
        $this->config = [
            'framework'                 => 'laravel',
            'bootstrap'                  => false,
            'semantic'                   => false,
            'template_source'            => __DIR__.'/../src/Templates/Laravel',
            '_sectionPrefix_'            => '',
            '_sectionTablePrefix_'       => '',
            '_sectionRoutePrefix_'       => '',
            '_sectionNamespace_'         => '',
            'relationships'              => null,
            'schema'                     => null,
            '_path_facade_'              => vfsStream::url('Facades'),
            '_path_service_'             => vfsStream::url('Services'),
            '_path_repository_'          => vfsStream::url('Repositories/'.ucfirst('testTable')),
            '_path_model_'               => vfsStream::url('Repositories/'.ucfirst('testTable')),
            '_path_controller_'          => vfsStream::url('Http/Controllers'),
            '_path_api_controller_'      => vfsStream::url('Http/Controllers/Api'),
            '_path_views_'               => vfsStream::url('resources/views'),
            '_path_tests_'               => vfsStream::url('tests'),
            '_path_request_'             => vfsStream::url('Http/Requests'),
            '_path_routes_'              => vfsStream::url('Http/routes.php'),
            '_path_api_routes_'          => vfsStream::url('Http/api-routes.php'),
            'routes_prefix'              => '',
            'routes_suffix'              => '',
            '_namespace_services_'       => 'App\Services',
            '_namespace_facade_'         => 'App\Facades',
            '_namespace_repository_'     => 'App\Repositories\\'.ucfirst('testTable'),
            '_namespace_model_'          => 'App\Repositories\\'.ucfirst('testTable'),
            '_namespace_controller_'     => 'App\Http\Controllers',
            '_namespace_api_controller_' => 'App\Http\Controllers\Api',
            '_namespace_request_'        => 'App\Http\Requests',
            '_lower_case_'               => strtolower('testTable'),
            '_lower_casePlural_'         => str_plural(strtolower('testTable')),
            '_camel_case_'               => ucfirst(camel_case('testTable')),
            '_camel_casePlural_'         => str_plural(camel_case('testTable')),
        ];

        $this->sectionedConfig = [
            '_path_facade_'              => 'Facades',
            '_path_service_'             => 'Services/_section_',
            '_path_repository_'          => 'Repositories/_section_/_table_',
            '_path_model_'               => 'Repositories/_section_/_table_',
            '_path_controller_'          => 'Http/Controllers/_section_/',
            '_path_api_controller_'      => 'Http/Controllers/Api/_section_/',
            '_path_views_'               => 'resources/views/_sectionLowerCase_',
            '_path_tests_'               => 'tests',
            '_path_request_'             => 'Http/Requests/_section_',
            '_path_routes_'              => 'Http/routes.php',
            '_path_api_routes_'          => 'Http/api-routes.php',
            'routes_prefix'              => "\n\nRoute::group(['namespace' => '_section_', 'prefix' => '_sectionLowerCase_', 'middleware' => ['web']], function () { \n",
            'routes_suffix'              => "\n});",
            '_app_namespace_'            => 'App\\',
            '_namespace_services_'       => 'App\Services\_section_',
            '_namespace_facade_'         => 'App\Facades',
            '_namespace_repository_'     => 'App\Repositories\_section_\_table_',
            '_namespace_model_'          => 'App\Repositories\_section_\_table_',
            '_namespace_controller_'     => 'App\Http\Controllers\_section_',
            '_namespace_api_controller_' => 'App\Http\Controllers\Api\_section_',
            '_namespace_request_'        => 'App\Http\Requests\_section_',
        ];
    }

    public function testBasicConfig()
    {
        $config = $this->service->basicConfig(
            'Laravel',
            'home/app',
            'home',
            'App\\',
            'books',
            [
                'api' => true,
                'apiOnly' => false,
                'ui' => 'bootstrap',
                'serviceOnly' => false,
                'withFacade' => false,
                'migration' => true,
                'schema' => 'id:increments,name:string',
                'relationships' => null,
            ]
        );

        $this->assertEquals($config['_path_controller_'], 'home/app/Http/Controllers/');
        $this->assertEquals($config['_path_api_controller_'], 'home/app/Http/Controllers/Api');
        $this->assertEquals($config['_ucCamel_casePlural_'], 'Books');
    }

    public function testSetConfig()
    {
        $config = $this->service->setConfig($this->sectionedConfig, 'admin', 'books');
        $this->assertEquals($config['_namespace_repository_'], 'App\Repositories\Admin\Books');
    }

    public function testConfigASectionedCRUD()
    {
        $config = $this->service->configASectionedCRUD($this->config, 'admin', 'books', ['admin', 'books']);
        $this->assertEquals($config['_namespace_repository_'], 'App\Repositories\Admin\Books');
        $this->assertEquals($config['_table_name_'], 'admin_books');
    }

    public function testGetTemplateConfig()
    {
        $config = $this->service->getTemplateConfig('Laravel', 'home');
        $this->assertContains('Templates/Laravel', $config);
    }
}
