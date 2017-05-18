<?php


class ArtisanTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

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
            '--schema' => 'id:increments,name:string(200),price:decimal(10,4),ibsn:integer|unsigned|references(\'id\')|on(\'products\')|onDelete(\'restrict\')',
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
        $file = $this->destinationDir.'/app/Models/Book.php';
        $this->assertTrue(file_exists($file));
        $contents = file_get_contents($file);
        $this->assertContains('class Book extends Model', $contents);
    }

    public function testSchema()
    {
        $files = glob($this->destinationDir.'/database/migrations/*_create_books_table.php');
        $this->assertTrue(file_exists($files[0]));
        $contents = file_get_contents($files[0]);
        $this->assertContains('$table->decimal(\'price\',10,4);', $contents);
        $this->assertContains('$table->integer(\'ibsn\')->unsigned()->references(\'id\')->on(\'products\')->onDelete(\'restrict\');', $contents);
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
        @unlink($files[0]);
    }
}
