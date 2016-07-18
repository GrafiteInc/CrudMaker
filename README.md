# CrudMaker

**CrudMaker** - An incredibly powerful and some say magical CRUD maker for Laravel

[![Codeship](https://img.shields.io/codeship/9c0c1620-2f3b-0134-5989-563e54af7ce1.svg?maxAge=2592000)](https://packagist.org/packages/yab/crudmaker)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/YABhq/CrudMaker/badges/quality-score.png?b=develop)](https://scrutinizer-ci.com/g/YABhq/CrudMaker/?branch=develop)
[![Packagist](https://img.shields.io/packagist/dt/yab/crudmaker.svg?maxAge=2592000)](https://packagist.org/packages/yab/crudmaker)
[![license](https://img.shields.io/github/license/mashape/apistatus.svg?maxAge=2592000)](https://packagist.org/packages/yab/crudmaker)

It can generate magical CRUD prototypes rapidly with full testing scripts prepared for you, requiring very little editing.

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
Yab\CrudMaker\CrudMakerProvider::class
```

Time to publish those assets!
```php
php artisan vendor:publish --provider="Yab\CrudMaker\CrudMakerProvider"
```

##### After these few steps you have the following tools at your fingertips:

## Commands
These commands build a CRUD with unit tests! Use the `table` command for tables that already exist.

```php
php artisan crudmaker:make {name or snake_names} {--api} {--ui=bootstrap|semantic} {--serviceOnly} {--withFacade} {--migration} {--schema=} {--relationships=}
php artisan crudmaker:table {name or snake_names} {--api} {--ui=bootstrap|semantic} {--serviceOnly} {--withFacade}
```

## License
CrudMaker is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)

### Bug Reporting and Feature Requests
Please add as many details as possible regarding submission of issues and feature requests

### Disclaimer
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
