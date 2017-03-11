<?php

use org\bovigo\vfs\vfsStream;

class ArtisanTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        // $this->config = [
        //     'framework' => 'laravel',
        //     'bootstrap' => false,
        //     'semantic' => false,
        //     'template_source' => __DIR__.'/../../src/Templates/Laravel',
        //     '_sectionPrefix_' => '',
        //     '_sectionTablePrefix_' => '',
        //     '_sectionRoutePrefix_' => '',
        //     '_sectionNamespace_' => '',
        //     'relationships' => null,
        //     'schema' => null,
        //     '_path_facade_' => vfsStream::url('Facades'),
        //     '_path_service_' => vfsStream::url('Services'),
        //     '_path_model_' => vfsStream::url('Models'),
        //     '_path_controller_' => vfsStream::url('Http/Controllers'),
        //     '_path_api_controller_' => vfsStream::url('Http/Controllers/Api'),
        //     '_path_views_' => vfsStream::url('resources/views'),
        //     '_path_tests_' => vfsStream::url('tests'),
        //     '_path_request_' => vfsStream::url('Http/Requests'),
        //     '_path_routes_' => vfsStream::url('Http/routes.php'),
        //     '_path_api_routes_' => vfsStream::url('Http/api-routes.php'),
        //     '_path_factory_' => vfsStream::url('database/factories/ModelFactory.php'),
        //     'routes_prefix' => '',
        //     'routes_suffix' => '',
        //     '_namespace_services_' => 'App\Services',
        //     '_namespace_facade_' => 'App\Facades',
        //     '_namespace_model_' => 'App\Models',
        //     '_namespace_controller_' => 'App\Http\Controllers',
        //     '_namespace_api_controller_' => 'App\Http\Controllers\Api',
        //     '_namespace_request_' => 'App\Http\Requests',
        //     '_lower_case_' => strtolower('testTable'),
        //     '_lower_casePlural_' => str_plural(strtolower('testTable')),
        //     '_camel_case_' => ucfirst(camel_case('testTable')),
        //     '_camel_casePlural_' => str_plural(camel_case('testTable')),
        //     '_ucCamel_casePlural_' => ucfirst(str_plural(camel_case('testTable'))),
        // ];

        $this->artisan('vendor:publish');

        $this->destinationDir = __DIR__.'/../../vendor/orchestra/testbench/fixture';

        if (!is_dir($this->destinationDir.'/routes')) {
            mkdir($this->destinationDir.'/routes');
        }

        file_put_contents($this->destinationDir.'/routes/web.php', "<?php\n\n");

        $this->artisan('crudmaker:new', [
            'table' => 'books',
            '--migration' => true,
            '--api' => true,
            '--ui' => 'bootstrap',
            '--schema' => 'id:increments,name:string',
        ]);
    }

    public function testApi()
    {
        $file = $this->destinationDir.'/app/Http/Controllers/Api/BooksController.php';
        $this->assertTrue(file_exists($file));
        $contents = file_get_contents($file);
        $this->assertContains('class BooksController extends Controller', $contents);
    }

    public function testController()
    {
        $file = $this->destinationDir.'/app/Http/Controllers/BooksController.php';
        $this->assertTrue(file_exists($file));
        $contents = file_get_contents($file);
        $this->assertContains('class BooksController extends Controller', $contents);
    }

    public function testModels()
    {
        $file = $this->destinationDir.'/app/Http/Controllers/BooksController.php';
        $this->assertTrue(file_exists($file));
        $contents = file_get_contents($file);
        $this->assertContains('class BooksController extends Controller', $contents);
    }

    public function testRequest()
    {
        $fileA = $this->destinationDir.'/app/Http/Requests/BookCreateRequest.php';
        $fileB = $this->destinationDir.'/app/Http/Requests/BookUpdateRequest.php';
        $this->assertTrue(file_exists($fileA));
        $this->assertTrue(file_exists($fileB));
        $contentsA = file_get_contents($fileA);
        $contentsB = file_get_contents($fileB);
        $this->assertContains('class BookCreateRequest extends FormRequest', $contentsA);
        $this->assertContains('class BookUpdateRequest extends FormRequest', $contentsB);
    }

    public function testService()
    {
        $file = $this->destinationDir.'/app/Services/BookService.php';
        $this->assertTrue(file_exists($file));
        $contents = file_get_contents($file);
        $this->assertContains('class BookService', $contents);
    }

    public function testRoutes()
    {
        $file = $this->destinationDir.'/routes/web.php';
        $contents = file_get_contents($file);

        $this->assertContains('BooksController', $contents);
        $this->assertContains('\'as\' => \'books.search\'', $contents);
        $this->assertContains('\'uses\' => \'BooksController@search\'', $contents);
    }

    public function testViews()
    {
        $fileA = $this->destinationDir.'/resources/views/books/index.blade.php';
        $contentsA = file_get_contents($fileA);
        $this->assertTrue(file_exists($fileA));
        $this->assertContains('$books', $contentsA);

        $fileB = $this->destinationDir.'/resources/views/books/edit.blade.php';
        $contentsB = file_get_contents($fileB);
        $this->assertTrue(file_exists($fileB));
        $this->assertContains('$book', $contentsB);
    }

    public function testTest()
    {
        $fileA = $this->destinationDir.'/tests/acceptance/BookAcceptanceTest.php';
        $contentsA = file_get_contents($fileA);
        $this->assertTrue(file_exists($fileA));
        $this->assertContains('class BookAcceptanceTest', $contentsA);

        $fileB = $this->destinationDir.'/tests/integration/BookServiceIntegrationTest.php';
        $contentsB = file_get_contents($fileB);
        $this->assertTrue(file_exists($fileB));
        $this->assertContains('class BookServiceIntegrationTest', $contentsB);
    }

    public function testFactory()
    {
        $file = $this->destinationDir.'/database/factories/ModelFactory.php';
        $contents = file_get_contents($file);

        $this->assertContains('Book::class', $contents);
        $this->assertContains('$factory->define(', $contents);
    }

    public function tearDown()
    {
        $files = glob($this->destinationDir.'/database/migrations/*_create_books_table.php');
        unlink($files[0]);
    }
}
