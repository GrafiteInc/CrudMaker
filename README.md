# CrudMaker

**CrudMaker** - An incredibly powerful and some say magical CRUD maker for Laravel

[![Codeship](https://img.shields.io/codeship/9c0c1620-2f3b-0134-5989-563e54af7ce1.svg)](https://packagist.org/packages/yab/crudmaker)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/YABhq/CrudMaker/badges/quality-score.png?b=develop)](https://scrutinizer-ci.com/g/YABhq/CrudMaker/?branch=develop)
[![Packagist](https://img.shields.io/packagist/dt/yab/crudmaker.svg)](https://packagist.org/packages/yab/crudmaker)
[![license](https://img.shields.io/github/license/mashape/apistatus.svg)](https://packagist.org/packages/yab/crudmaker)

It can generate magical CRUD prototypes rapidly with full testing scripts prepared for you, requiring very little editing. Following SOLID principals it can construct a basic set of components pending on a table name provided in the CLI. The CRUD can be used with singular table entities think: 'books' or 'authors' but, you can also build CRUDs for combined entities that is a parent, and child like structure: 'books_authors'. This will generate a 'books_authors' table and place all components of the authors (controller, repository, model etc) into a Books namespace, which means you can then generate 'books_publishers' and have all the components be added as siblings to the authors. Now let's say you went ahead with using the Laracogs starter kit, then you can autobuild your CRUDs with them bootstrapped, which means they're already wrapped up as view extensions of the dashboard content which means you're even closer to being done your application.

##### Author(s):
* [Matt Lantz](https://github.com/mlantz) ([@mattylantz](http://twitter.com/mattylantz), matt at yabhq dot com)
* [Chris Blackwell](https://github.com/chrisblackwell) ([@chrisblackwell](https://twitter.com/chrisblackwell), chris at yabhq dot com)

## Requirements

1. PHP 5.6+
2. OpenSSL
3. Laravel 5.1+

----

### Installation

Start a new Laravel project:
```php
composer create-project laravel/laravel your-project-name
```

Then run the following to add CrudMaker
```php
composer require "yab/crudmaker"
```

Add this to the `config/app.php` in the providers array:
```php
// Laravel
Yab\CrudMaker\CrudMakerProvider::class

// Lumen
$app->register(Yab\CrudMaker\LumenCrudMakerProvider::class)
```

Time to publish those assets!
```php
// Laravel
php artisan vendor:publish --provider="Yab\CrudMaker\CrudMakerProvider"

// Lumen
php artisan crudmaker:init
```

##### After these few steps you have the following tools at your fingertips:

## Commands
These commands build a CRUD with unit tests! Use the `table` command for tables that already exist.

```php
php artisan crudmaker:make {name or snake_names} {--api} {--ui=bootstrap|semantic} {--serviceOnly} {--withFacade} {--migration} {--schema=} {--relationships=}
php artisan crudmaker:table {name or snake_names} {--api} {--ui=bootstrap|semantic} {--serviceOnly} {--withFacade}
```

---

## Command Options

### API

The API option will add in a controller to handle API requests and responses. It will also add in the API routes assuming this is v1.

### UI
There are two primarily supported CSS frameworks (Bootstrap and Semantic), you can opt in for either or disregard them completely. Both expect a dashboard parent view.

### Service Only
The service only will allow you to generate CRUDs that are service layer and lower this includes: Service, Repository, Model, and Tests with the options for migrations. It will skip the Controllers, Routes, Views, etc. This keeps your code lean, and is optimal for relationships that don't maintain a 'visual' presence in your site/app such as downloads of an entity.

### Migration
The migration option will add the migration file to your migrations directory, using the schema builder will fill in the create table method.

### Schema (Requires migration option)
You can define the table schema with the structure below. The field types should match what would be the Schema builder.

```
--schema="id:increments,name:string"
```

The following column types are available:
 * bigIncrements
 * increments
 * bigInteger
 * binary
 * boolean
 * char
 * date
 * dateTime
 * decimal
 * double
 * enum
 * float
 * integer
 * ipAddress
 * json
 * jsonb
 * longText
 * macAddress
 * mediumInteger
 * mediumText
 * morphs
 * smallInteger
 * string
 * string
 * text
 * time
 * tinyInteger
 * timestamp
 * uuid

### Relationships (Requires migration option)
You can specifiy relationships, in order to automate a few more steps of building your CRUDs. You can set the relationship expressions like this:

`relation|class|name`

or something like:

`hasOne|App\Author|author`

This will add in the relationships to your models, as well as add the needed name_id field to your tables. Just one more thing you don't have to worry about.

### With Facades
If you opt in for Facades the CRUD will generate them, with the intention that they will be used to access the service. You will need to bind them to the app in your own providers, but you will at least have the Facade file generated.

## Templates
All generated components are based on templates. There are basic templates included in this package, however in most cases you will have to conform them to your project's needs. If you have published the assets during the installation process, the template files will be available in `resources/crudmaker/crud`.

Test templates are treated differently from the other templates. By default there are three test templates provided, two integration tests for the generated service and repository, and one acceptance test. However, the Tests folder has a 'one to one' mapping with the tests folder of your project. This means you can easily add tests for different test levels matching your project. For example, if you want to create a unit test for the generated controller, you can just create a new template file, for instance `resources/crudmaker/crud/Tests/Unit/ControllerTest.txt`. When running the CRUD generator, the following file will then be created: `tests/unit/NameOfResourceControllerTest.php`.

## Examples
The following components are generated:

Files Generated
------
* Controller
* Api Controller (optional)
* Service
* Repository
* Request
* Model
* Facade (optional)
* Views (Bootstrap or Semantic or CSS framework-less)
* Tests
* Migration (optional)
Appends to the following Files:
* app/Http/routes.php
* database/factories/ModelFactory.php

Single Word Example (Book):
------
```
php artisan crudmaker:make Book --migration --schema="id:increments,title:string,author:string"
```

When using the default paths for the components, the following files will be generated:

* app/Http/Controllers/BookController.php
* app/Http/Requests/BookRequest.php
* app/Repositories/Book/BookRepository.php
* app/Repositories/Book/Book.php
* app/Services/BookService.php
* resources/views/book/create.blade.php
* resources/views/book/edit.blade.php
* resources/views/book/index.blade.php
* resources/views/book/show.blade.php
* database/migrations/create_books_table.php
* tests/BookIntegrationTest.php
* tests/BookRepositoryTest.php
* tests/BookServiceTest.php

Snake Name Example (Book_Author):
------
```
php artisan crudmaker:make Book_Author --migration --schema="id:increments,firstname:string,lastname:string" --withFacade
```

When using the default paths for the components, the following files will be generated:

* app/Facades/Books/AuthorServiceFacade.php
* app/Http/Controllers/Books/AuthorController.php
* app/Http/Requests/Books/AuthorRequest.php
* app/Repositories/Books/Author/AuthorRepository.php
* app/Repositories/Books/Author/Author.php
* app/Services/Books/AuthorService.php
* resources/views/book/author/create.blade.php
* resources/views/book/author/edit.blade.php
* resources/views/book/author/index.blade.php
* resources/views/book/author/show.blade.php
* database/migrations/create_book_authors_table.php
* tests/Books/AuthorIntegrationTest.php
* tests/Books/AuthorRepositoryTest.php
* tests/Books/AuthorServiceTest.php

Single Name Example (Book with API):
------
```
php artisan crudmaker:make Book --api --migration --schema="id:increments,title:string,author:string"
```

When using the default paths for the components, the following files will be generated:

* app/Http/Controllers/Api/BookController.php
* app/Http/Controllers/BookController.php
* app/Http/Requests/BookRequest.php
* app/Repositories/Book/BookRepository.php
* app/Repositories/Book/Book.php
* app/Services/BookService.php
* resources/views/book/create.blade.php
* resources/views/book/edit.blade.php
* resources/views/book/index.blade.php
* resources/views/book/show.blade.php
* database/migrations/create_books_table.php
* tests/BookIntegrationTest.php
* tests/BookRepositoryTest.php
* tests/BookServiceTest.php

This is an example of what would be generated with the CRUD builder. It has all basic CRUD methods set.

---

## License
CrudMaker is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)

### Bug Reporting and Feature Requests
Please add as many details as possible regarding submission of issues and feature requests

### Disclaimer
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
