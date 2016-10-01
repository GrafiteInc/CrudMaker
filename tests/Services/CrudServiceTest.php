<?php

use org\bovigo\vfs\vfsStream;
use Yab\CrudMaker\Services\CrudService;

class MockProgressBar
{
    public function advance()
    {
        return true;
    }
}

class CrudServiceTest extends TestCase
{
    protected $service;
    protected $config;
    protected $command;
    protected $bar;

    public function setUp()
    {
        parent::setUp();

        $this->command = Mockery::mock(\Illuminate\Console\Command::class);
        $this->command->shouldReceive('callSilent')->andReturnUsing(function ($command, $data) {
            \Artisan::call($command, $data);
        });
        $this->bar = Mockery::mock('MockProgressBar')
            ->shouldReceive('advance')
            ->andReturn(true)
            ->getMock();
        $this->service = app(CrudService::class);
        $this->config = [
            'framework'                  => 'Laravel',
            'bootstrap'                  => false,
            'semantic'                   => false,
            'relationships'              => null,
            'schema'                     => null,
            '_path_facade_'              => vfsStream::url('Facades'),
            '_path_service_'             => vfsStream::url('Services'),
            '_path_model_'               => vfsStream::url('Models'),
            '_path_controller_'          => vfsStream::url('Http/Controllers'),
            '_path_api_controller_'      => vfsStream::url('Http/Controllers/Api'),
            '_path_views_'               => vfsStream::url('resources/views'),
            '_path_tests_'               => vfsStream::url('tests'),
            '_path_request_'             => vfsStream::url('Http/Requests'),
            '_path_migrations_'          => 'database/migrations',
            '_path_routes_'              => vfsStream::url('Http/routes.php'),
            '_path_api_routes_'          => vfsStream::url('Http/api-routes.php'),
            'routes_prefix'              => '',
            'routes_suffix'              => '',
            '_namespace_services_'       => 'App\Services',
            '_namespace_facade_'         => 'App\Facades',
            '_namespace_model_'          => 'App\Models',
            '_namespace_controller_'     => 'App\Http\Controllers',
            '_namespace_api_controller_' => 'App\Http\Controllers\Api',
            '_namespace_request_'        => 'App\Http\Requests',
            '_lower_case_'               => strtolower('testTable'),
            '_lower_casePlural_'         => str_plural(strtolower('testTable')),
            '_camel_case_'               => ucfirst(camel_case('testTable')),
            '_camel_casePlural_'         => str_plural(camel_case('testTable')),
            '_ucCamel_casePlural_'       => ucfirst(str_plural(camel_case('testTable'))),
            'template_source'            => __DIR__.'/../../src/Templates/Laravel',
            'options-serviceOnly'        => false,
            'options-apiOnly'            => false,
            'options-withFacade'         => false,
            'options-migration'          => true,
            'options-api'                => true,
            'options-schema'             => 'id:increments,name:string',
        ];
    }

    public function testGenerateCore()
    {
        $crud = vfsStream::setup("/");

        $this->service->generateCore($this->config, $this->bar);
        $modelContents = $crud->getChild('Models/TestTable.php');
        $serviceContents = $crud->getChild('Services/TestTableService.php');

        $this->assertTrue($crud->hasChild('Services/TestTableService.php'));
        $this->assertContains('class TestTable', $modelContents->getContent());
        $this->assertContains('class TestTableService', $serviceContents->getContent());
    }

    public function testGenerateAppBased()
    {
        $crud = vfsStream::setup("/");
        $crud->addChild(vfsStream::newDirectory('Http'));

        $this->service->generateAppBased($this->config, $this->bar);
        $controllerContents = $crud->getChild('Http/Controllers/TestTablesController.php');
        $routesContents = $crud->getChild('Http/routes.php');

        $this->assertTrue($crud->hasChild('Http/Controllers/TestTablesController.php'));
        $this->assertContains('class TestTablesController', $controllerContents->getContent());
        $this->assertContains('TestTableController', $routesContents->getContent());
    }

    public function testGenerateAPI()
    {
        $crud = vfsStream::setup("/");
        $crud->addChild(vfsStream::newDirectory('Http'));

        $this->service->generateAPI($this->config, $this->bar);
        $controllerContents = $crud->getChild('Http/Controllers/Api/TestTablesController.php');
        $routesContents = $crud->getChild('Http/api-routes.php');

        $this->assertTrue($crud->hasChild('Http/Controllers/Api/TestTablesController.php'));
        $this->assertContains('class TestTablesController', $controllerContents->getContent());
        $this->assertContains('TestTableController', $routesContents->getContent());
    }
}
