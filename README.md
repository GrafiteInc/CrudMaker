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
| 5.6.x | 1.4.x | yes |
| 5.5.x | 1.3.x | no |
| 5.4.x | 1.2.x | no |
| 5.3.x | 1.1.x | no |

## Documentation

[https://docs.grafite.ca/others/crud](https://docs.grafite.ca/others/crud)

## License
CrudMaker is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)

### Bug Reporting and Feature Requests
Please add as many details as possible regarding submission of issues and feature requests

### Disclaimer
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
