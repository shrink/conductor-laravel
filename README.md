# Conductor: Laravel

[![Packagist](https://img.shields.io/packagist/v/shrink/conductor-laravel.svg)][packagist]

[Conductor][conductor] checks the status of runtime dependencies, **Conductor:
Laravel** is a set of Laravel runtime dependency checks.

> **Scenario**: I have a containerised Laravel application, I want to deploy a
  new version which includes database migrations. How do I prevent the new
  version from running against a database that has yet to be migrated?

## Usage

1. [**Install**](#install) the library with composer
2. [**Enable**](#enable) Conductor with the optional Service Provider
3. [**Define**](#define-dependencies) your application's runtime dependencies

[&darr; Jump to **Dependencies** available for Laravel](#supported-dependencies)

### Install

```console
dev:~$ composer require shrink/conductor-laravel
```

### Enable

An optional out-of-the-box [Service Provider][service-providers] registers an
array of dependencies and an HTTP endpoint.

```diff
'providers' => [
    // ...
+   Shrink\Conductor\Laravel\Conductor::class,
    // ...
];
```

#### Without Package Service Provider

The [Database Schema dependency](#database-schema) relies on the Database
Migrator to determine Schema status, however because Laravel's standard
behaviour is to
[register the Migrator to a `migrator` alias][migrator-registration] you will
need to re-bind `migrator` to the Migrator class name.

```php
$app->bind(\Illuminate\Database\Migrations\Migrator::class, 'migrator');
```

### Define Dependencies

A Dependency accepts a string identifier and an object that implements the
`ChecksDependencyStatus` interface provided by Conductor.

```php
new Dependency(
    'identifier',
    new Shrink\Conductor\ChecksDependencyStatus::class
);
```

### Supported Dependency Checks

#### Database Schema

[Database Schema][database-schema-dependency] checks that the connected database
is running a compatible version of the database schema. A database schema is
compatible when every migration (on the filesystem) has been applied to the
database.

Depends on a [`lcobucci/clock`][lcobucci-clock] implementation.

```php
use Illuminate\Database\Migrations\Migrator;
use Lcobucci\Clock\Clock;
use Shrink\Conductor\Laravel\Dependencies\DatabaseSchema;

new DatabaseSchema(
    $app->make(Migrator::class, 'migrator'),
    (string) $app->basePath('database/migrations'),
    $app->make(Clock::class)
);
```

## Development

### Hooks

A pre-commit Git Hook is included for ensuring compliance with code
requirements on commit, enable the Git Hook by running the following command:

```console
dev:~$ git config core.hooksPath .github/hooks
```

## License

Conductor: Laravel is open-sourced software licensed under the
[MIT license][mit-license].

[conductor]: https://github.com/shrink/conductor
[packagist]: https://packagist.org/packages/shrink/conductor-laravel
[mit-license]: https://choosealicense.com/licenses/mit/
[service-providers]: https://laravel.com/docs/8.x/providers
[migrator-registration]: https://github.com/laravel/framework/blob/8.x/src/Illuminate/Database/MigrationServiceProvider.php
[database-schema-dependency]: src/Laravel/Dependencies/DatabaseSchema.php
[lcobucci-clock]: https://github.com/lcobucci/clock
