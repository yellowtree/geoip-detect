# Codacy PHP Coverage Reporter
[Codacy](https://codacy.com/) coverage support for PHP. Get coverage reporting and code analysis for PHP from Codacy.

[![Codacy Badge](https://www.codacy.com/project/badge/d992a862b1994805907ec277e16b0fda)](https://www.codacy.com/public/caxaria/php-codacy-coverage)

# Prerequisites

- PHP 5.3 or later
- Clover style coverage report

# Installation

Setup codacy-coverage with Composer, just add the following to your composer.json:

```js
// composer.json
{
    "require-dev": {
        "codacy/coverage": "dev-master"
    }
}
```

Download the dependencies by running Composer in the directory of your `composer.json`:

```sh
# install
$ php composer.phar install --dev
# update
$ php composer.phar update codacy/coverage --dev
```

codacy-coverage library is available on [Packagist](https://packagist.org/packages/codacy/coverage).

Add the autoloader to your php script:

```php
require_once 'vendor/autoload.php';
```

## PHPUnit

Make sure that `phpunit.xml.dist` is configured to generate "coverage-clover" type log named `clover.xml` like the following configuration:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit ...>
    <logging>
        ...
        <log type="coverage-clover" target="build/logs/clover.xml"/>
        ...
    </logging>
</phpunit>
```

## Travis CI

Add codacycoverage to your `.travis.yml`:

```yml
# .travis.yml
language: php
php:
  - 5.3
  - 5.4
  - 5.5
  - 5.6
  - hhvm

before_script:
  - curl -s http://getcomposer.org/installer | php
  - php composer.phar install -n

script:
  - php vendor/bin/phpunit

after_script:
  - php vendor/bin/codacycoverage clover
```

## License
[MIT](LICENSE)
