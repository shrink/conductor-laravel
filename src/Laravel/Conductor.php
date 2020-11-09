<?php

declare(strict_types=1);

namespace Shrink\Conductor\Laravel;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Routing\Router;
use Illuminate\Routing\RouteRegistrar;
use Illuminate\Support\ServiceProvider;
use Shrink\Conductor\Laravel\Dependencies\DatabaseSchema;
use Shrink\Conductor\Laravel\Http\AttachDependencyParameter;
use Shrink\Conductor\Laravel\Http\ShowStatus;

final class Conductor extends ServiceProvider
{
    public function boot(Application $app, RouteRegistrar $registrar): void
    {
        $app->bind(Migrator::class, 'migrator');

        $app->when(DatabaseSchema::class)
            ->needs('$migrationsPath')
            ->give((string) $app->basePath('database/migrations'));

        /** @psalm-var \Shrink\Conductor\Laravel\Dependencies\DatabaseSchema */
        $databaseSchemaDependency = $app->make(DatabaseSchema::class);

        $dependencies = new DependenciesArray(
            new Dependency('database', $databaseSchemaDependency)
        );

        $app->instance(CollectsApplicationDependencies::class, $dependencies);

        $this->registerHttpEndpoints($app, $registrar);
    }

    private function registerHttpEndpoints(
        Application $app,
        RouteRegistrar $registrar
    ): void {
        $app->when(AttachDependencyParameter::class)
            ->needs('$parameter')
            ->give('dependency');

        $endpoints = static function (Router $router): void {
            $router->get('/{dependency}', ShowStatus::class);
        };

        $registrar
            ->middleware(AttachDependencyParameter::class)
            ->prefix('.conductor')
            ->group($endpoints);
    }
}
