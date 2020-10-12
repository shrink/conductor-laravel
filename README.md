# Conductor: Laravel

[![Packagist](https://img.shields.io/packagist/v/shrink/conductor-laravel.svg)][packagist]

[Conductor][conductor] checks the status of runtime dependencies, **Conductor:
Laravel** is a set of Laravel runtime dependency checks.

## Usage

1. [**Install**](#install) the library with composer

[&darr; Jump to **Dependencies** available for Laravel](#dependencies)

### Install

```console
dev:~$ composer require shrink/conductor-laravel
```

### Dependencies

#### Database Schema

Database Schema checks that the database the application is connected to is
running a compatible version of the database schema.

This check is helpful when running Laravel in a containerised environment, where
many versions of the application may be running against a single database which
is migrated independent of any one container.

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
