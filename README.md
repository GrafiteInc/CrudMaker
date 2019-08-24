# CrudMaker

**CrudMaker** - An incredibly powerful and some say magical CRUD maker for Laravel

[![Build Status](https://travis-ci.org/GrafiteInc/CrudMaker.svg?branch=master)](https://travis-ci.org/GrafiteInc/CrudMaker)
[![Maintainability](https://api.codeclimate.com/v1/badges/6398c82f417803d3fe6e/maintainability)](https://codeclimate.com/github/GrafiteInc/CrudMaker/maintainability)
[![Packagist](https://img.shields.io/packagist/dt/grafite/crudmaker.svg)](https://packagist.org/packages/grafite/crudmaker)
[![license](https://img.shields.io/github/license/mashape/apistatus.svg)](https://packagist.org/packages/grafite/crudmaker)

It can generate magical CRUD prototypes rapidly with full testing scripts prepared for you, requiring very little editing. Following SOLID principals it can construct a basic set of components pending on a table name provided in the CLI. The CRUD can be used with singular table entities think: 'books' or 'authors' but, you can also build CRUDs for combined entities that is a parent, and child like structure: 'books_authors'. This will generate a 'books_authors' table and place all components of the authors (controller, service, model etc) into a Books namespace, which means you can then generate 'books_publishers' and have all the components be added as siblings to the authors. Now let's say you went ahead with using the Laracogs starter kit, then you can autobuild your CRUDs with them bootstrapped, which means they're already wrapped up as view extensions of the dashboard content which means you're even closer to being done your application.

##### Author(s):
* [Matt Lantz](https://github.com/mlantz) ([@mattylantz](http://twitter.com/mattylantz), mattlantz at gmail dot com)

## Requirements

1. PHP 7+
2. OpenSSL

## Compatibility and Support

| Laravel Version | Package Tag | Supported |
|-----------------|-------------|-----------|
| 5.6.x | 1.4.x | no |
| 5.5.x | 1.3.x | no |
| 5.4.x | 1.2.x | no |
| 5.3.x | 1.1.x | no |

## Documentation

```
php artisan crudmaker:new {name or snake_names}
{--api}
{--ui=bootstrap}
{--serviceOnly}
{--withBaseService}
{--withFacade}
{--withoutViews}
{--migration}
{--schema}
{--relationships}
```

## Config
The config is published in `config` where you can specify namespaces, locations etc.

## Templates
All generated components are based on templates. There are basic templates included in this package, however in most cases you will have to conform them to your project's needs. If you have published the assets during the installation process, the template files will be available in `resources/crudmaker/crud`.

Test templates are treated differently from the other templates. By default there are two test templates provided, one integration test for the generated service, and one acceptance test. However, the Tests folder has a 'one to one' mapping with the tests folder of your project. This means you can easily add tests for different test levels matching your project. For example, if you want to create a unit test for the generated controller, you can just create a new template file, for instance `resources/crudmaker/crud/Tests/Unit/ControllerTest.txt`. When running the CRUD generator, the following file will then be created: `tests/unit/NameOfResourceControllerTest.php`.

## API
The API option will add in a controller to handle API requests and responses. It will also add in the API routes assuming this is v1.

```
yourapp.com/api/v1/books
```

## UI
There is only one supported CSS framework (Bootstrap). Without the UI option specified the CrudMaker will use the plain HTML view which isn't very nice looking.

Both expect a dashboard parent view, this can be generated with the following commands:
```
artisan grafite:build bootstrap
```

These re-skin your views with either of the CSS frameworks.

## Service Only
The service only will allow you to generate CRUDs that are service layer and lower this includes: Service, Model, and Tests with the options for migrations. It will skip the Controllers, Routes, Views, etc. This keeps your code lean, and is optimal for relationships that don't maintain a 'visual' presence in your site/app such as downloads of an entity.

## With Base Service
If you opt in for this the CrudMaker will generate a BaseService which this CRUD's service will extend from. This can be handy when you want to reduce code duplication.

## With Facades
If you opt in for Facades the CRUD will generate them, with the intention that they will be used to access the service. You will need to bind them to the app in your own providers, but you will at least have the Facade file generated.

## Migration
The migration option will add the migration file to your migrations directory, using the schema builder will fill in the create table method. The Schema and Relationships require this since they expect to modify the migration file generated.

## Schema
*Requires migration option*

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
* text
* time
* tinyInteger
* timestamp
* uuid

#### Want further definitions?

```
--schema="id:increments|first,user_id:integer|unsigned,name:string|nullable|after('id'),age:integer|default(0)"
```

You can even handle some parameters such as:

```
--schema="id:increments|first,user_id:integer|unsigned,name:string(45)"
```

## Relationships
*Requires migration option*

You can specify relationships, in order to automate a few more steps of building your CRUDs. You can set the relationship expressions like this:

```
relation|class|column
```

or something like:

```
hasOne|App\Author|author
```

This will add in the relationships to your models, as well as add the needed name_id field to your tables. Just one more thing you don't have to worry about.
The general relationships handled by the HTML rendered are:

```
hasOne
hasMany
belongsTo
```

!!! warning "The CRUD currently doesn't support `belongsToMany` that is to say it does not currently create a relational table"

## Examples
The following components are generated:

#### Files Generated

* Controller
* Api Controller (optional)
* Service
* CreateRequest
* UpdateRequest
* Model
* Facade (optional)
* Views (Bootstrap or Semantic or CSS framework-less)
* Tests
* Migration (optional)
Appends to the following Files:
* app/Http/routes.php
* database/factories/ModelFactory.php

#### Single Word Example (Book):
```
php artisan crudmaker:new Book
--migration
--schema="id:increments,title:string,author:string"
```

When using the default paths for the components, the following files will be generated:

* app/Http/Controllers/BookController.php
* app/Http/Requests/BookCreateRequest.php
* app/Http/Requests/BookUpdateRequest.php
* app/Models/Book/Book.php
* app/Services/BookService.php
* resources/views/book/create.blade.php
* resources/views/book/edit.blade.php
* resources/views/book/index.blade.php
* resources/views/book/show.blade.php
* database/migrations/create_books_table.php
* tests/BookIntegrationTest.php
* tests/BookServiceTest.php

#### Snake Name Example (Book_Author):
```
php artisan crudmaker:new Book_Author
--migration
--schema="id:increments,firstname:string,lastname:string"
--withFacade
```

When using the default paths for the components, the following files will be generated:

* app/Facades/Books/AuthorServiceFacade.php
* app/Http/Controllers/Books/AuthorController.php
* app/Http/Requests/Books/AuthorCreateRequest.php
* app/Http/Requests/Books/AuthorUpdateRequest.php
* app/Models/Books/Author/Author.php
* app/Services/Books/AuthorService.php
* resources/views/book/author/create.blade.php
* resources/views/book/author/edit.blade.php
* resources/views/book/author/index.blade.php
* resources/views/book/author/show.blade.php
* database/migrations/create_book_authors_table.php
* tests/Books/AuthorIntegrationTest.php
* tests/Books/AuthorServiceTest.php

#### Single Name Example (Book with API):
```
php artisan crudmaker:new Book
--api
--migration
--schema="id:increments,title:string,author:string"
```

When using the default paths for the components, the following files will be generated:

* app/Http/Controllers/Api/BookController.php
* app/Http/Controllers/BookController.php
* app/Http/Requests/BookCreateRequest.php
* app/Http/Requests/BookUpdateRequest.php
* app/Models/Book/Book.php
* app/Services/BookService.php
* resources/views/book/create.blade.php
* resources/views/book/edit.blade.php
* resources/views/book/index.blade.php
* resources/views/book/show.blade.php
* database/migrations/create_books_table.php
* tests/BookIntegrationTest.php
* tests/BookServiceTest.php

This is an example of what would be generated with the CRUD builder. It has all basic CRUD methods set.

## Table CRUD

The table CRUD is a wrapper on the CRUD which will parse the table in the database and build the CRUD from that table.

*You must make sure the name matches the table name case wise*

```
php artisan crudmaker:table {name or snake_names}
{--api}
{--ui=bootstrap}
{--serviceOnly}
{--withFacade}
{--relationships}
```

## License
CrudMaker is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)

### Bug Reporting and Feature Requests
Please add as many details as possible regarding submission of issues and feature requests

### Disclaimer
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
