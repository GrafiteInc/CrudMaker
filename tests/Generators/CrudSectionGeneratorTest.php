<?php

use org\bovigo\vfs\vfsStream;
use Yab\CrudMaker\Generators\CrudGenerator;

class CrudSectionGeneratorTest extends PHPUnit_Framework_TestCase
{
    protected $generator;
    protected $config;

    public function setUp()
    {
        $this->generator = new CrudGenerator();
        $this->config = [
            'framework' => 'laravel',
            'bootstrap' => false,
            'semantic' => false,
            'template_source' => __DIR__.'/../../src/Templates/Laravel',
            '_sectionPrefix_' => 'superman.',
            '_sectionTablePrefix_' => 'superman_',
            '_sectionRoutePrefix_' => 'superman/',
            '_sectionNamespace_' => 'Superman\\',
            'relationships' => null,
            'schema' => null,
            '_path_facade_' => vfsStream::url('Facades'),
            '_path_service_' => vfsStream::url('Services/Superman'),
            '_path_model_' => vfsStream::url('Models/Superman'),
            '_path_controller_' => vfsStream::url('Http/Controllers/Superman'),
            '_path_api_controller_' => vfsStream::url('Http/Controllers/Superman/Api'),
            '_path_views_' => vfsStream::url('resources/views/superman'),
            '_path_tests_' => vfsStream::url('tests'),
            '_path_request_' => vfsStream::url('Http/Requests/Superman'),
            '_path_routes_' => vfsStream::url('Http/routes.php'),
            '_path_api_routes_' => vfsStream::url('Http/api-routes.php'),
            '_path_factory_' => vfsStream::url('database/factories/ModelFactory.php'),
            'routes_prefix' => "\n\nRoute::group(['namespace' => 'Superman', 'prefix' => 'superman', 'middleware' => ['web']], function () { \n",
            'routes_suffix' => "\n});",
            '_namespace_services_' => 'App\Services\Superman',
            '_namespace_facade_' => 'App\Facades',
            '_namespace_model_' => 'App\Models\Superman',
            '_namespace_controller_' => 'App\Http\Controllers\Superman',
            '_namespace_api_controller_' => 'App\Http\Controllers\Superman\Api',
            '_namespace_request_' => 'App\Http\Requests\Superman',
            '_lower_case_' => strtolower('testTable'),
            '_lower_casePlural_' => str_plural(strtolower('testTable')),
            '_camel_case_' => ucfirst(camel_case('testTable')),
            '_camel_casePlural_' => str_plural(camel_case('testTable')),
            '_ucCamel_casePlural_' => ucfirst(str_plural(camel_case('testTable'))),
            '_table_name_' => 'superman_testtable',
        ];
    }

    public function testApiGenerator()
    {
        $this->crud = vfsStream::setup('Http/Controllers/Superman/Api');

        $this->generator->createApi($this->config, false);
        $contents = $this->crud->getChild('Http/Controllers/Superman/Api/TestTablesController.php');

        $this->assertTrue($this->crud->hasChild('Http/Controllers/Superman/Api/TestTablesController.php'));
        $this->assertContains('class TestTablesController extends Controller', $contents->getContent());
    }

    public function testControllerGenerator()
    {
        $this->crud = vfsStream::setup('Http/Controllers');
        $this->generator->createController($this->config);

        $this->assertTrue($this->crud->hasChild('Http/Controllers/Superman/TestTablesController.php'));
        $contents = $this->crud->getChild('Http/Controllers/Superman/TestTablesController.php');

        $this->assertContains('class TestTablesController extends Controller', $contents->getContent());
    }

    public function testModelGenerator()
    {
        $this->crud = vfsStream::setup('Models');

        $this->generator->createModel($this->config);
        $contents = $this->crud->getChild('Models/Superman/TestTable.php');

        $this->assertTrue($this->crud->hasChild('Models/Superman/TestTable.php'));
        $this->assertContains('class TestTable', $contents->getContent());
    }

    public function testRequestGenerator()
    {
        $this->crud = vfsStream::setup('Http/Requests');

        $this->generator->createRequest($this->config);
        $contents = $this->crud->getChild('Http/Requests/Superman/TestTableCreateRequest.php');

        $this->assertTrue($this->crud->hasChild('Http/Requests/Superman/TestTableCreateRequest.php'));
        $this->assertContains('class TestTableCreateRequest', $contents->getContent());
    }

    public function testServiceGenerator()
    {
        $this->crud = vfsStream::setup('Services');

        $this->generator->createService($this->config);
        $contents = $this->crud->getChild('Services/Superman/TestTableService.php');

        $this->assertTrue($this->crud->hasChild('Services/Superman/TestTableService.php'));
        $this->assertContains('class TestTableService', $contents->getContent());
    }

    public function testRoutesGenerator()
    {
        $this->crud = vfsStream::setup('Http');
        file_put_contents(vfsStream::url('Http/routes.php'), 'test');

        $this->generator->createRoutes($this->config, false);
        $contents = $this->crud->getChild('Http/routes.php');

        $this->assertContains('TestTablesController', $contents->getContent());
        $this->assertContains('\'as\' => \'superman.testtables.search\'', $contents->getContent());
        $this->assertContains('\'uses\' => \'TestTablesController@search\'', $contents->getContent());
    }

    public function testViewsGenerator()
    {
        $this->crud = vfsStream::setup('resources/views');

        $this->generator->createViews($this->config);
        $contents = $this->crud->getChild('resources/views/superman/testtables/index.blade.php');

        $this->assertTrue($this->crud->hasChild('resources/views/superman/testtables/index.blade.php'));
        $this->assertContains('$testtable', $contents->getContent());
    }

    public function testTestGenerator()
    {
        $this->crud = vfsStream::setup('tests');

        $this->assertTrue($this->generator->createTests($this->config, false));

        $contents = $this->crud->getChild('tests/acceptance/TestTableAcceptanceTest.php');
        $this->assertTrue($this->crud->hasChild('tests/acceptance/TestTableAcceptanceTest.php'));
        $this->assertContains('class TestTableAcceptanceTest', $contents->getContent());

        $contents = $this->crud->getChild('tests/integration/TestTableServiceIntegrationTest.php');
        $this->assertTrue($this->crud->hasChild('tests/integration/TestTableServiceIntegrationTest.php'));
        $this->assertContains('class TestTableServiceIntegrationTest', $contents->getContent());
    }

    public function testTestGeneratorServiceOnly()
    {
        $this->crud = vfsStream::setup('tests');

        $this->assertTrue($this->generator->createTests($this->config, true));

        $this->assertFalse($this->crud->hasChild('tests/acceptance/TestTableAcceptanceTest.php'));

        $contents = $this->crud->getChild('tests/integration/TestTableServiceIntegrationTest.php');
        $this->assertTrue($this->crud->hasChild('tests/integration/TestTableServiceIntegrationTest.php'));
        $this->assertContains('class TestTableServiceIntegrationTest', $contents->getContent());
    }

    public function testFactoryGenerator()
    {
        $this->crud = vfsStream::setup('database/factories');
        file_put_contents(vfsStream::url('database/factories/ModelFactory.php'), 'test');

        $this->generator->createFactory($this->config);
        $contents = $this->crud->getChild('database/factories/ModelFactory.php');

        $this->assertContains('TestTable::class', $contents->getContent());
        $this->assertContains('$factory->define(', $contents->getContent());
    }
}
