<?php

declare(strict_types=1);

namespace Shrink\Conductor\Laravel;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Routing\Router;
use Illuminate\Routing\RouteRegistrar;
use Illuminate\Support\ServiceProvider;
use Shrink\Conductor\Laravel\Dependencies\DatabaseSchema;
use Shrink\Conductor\Laravel\Http\AttachDependencyCheckParameter;
use Shrink\Conductor\Laravel\Http\ShowStatus;

final class Conductor extends ServiceProvider
{
    /**
     * Bind a simple Dependency Collection and register HTTP endpoints which
     * expose the status of each Dependency.
     */
    public function boot(Application $app, RouteRegistrar $registrar): void
    {
        $app->instance(
            CollectsApplicationDependencyChecks::class,
            new DependencyChecksArray()
        );

        $this->bindDatabaseSchemaCheck($app);
        $this->registerHttpEndpoints($app, $registrar);
    }

    /**
     * The Database Schema Check cannot be resolved out of the Laravel container
     * automatically because it depends on the Migrator, which Laravel does not
     * register by its class name -- instead it is registered by a name
     * ("migrator") -- so we must bind it by the class name. Additionally, the
     * path to the database migrations on the filesystem must be explicitly
     * bound.
     */
    private function bindDatabaseSchemaCheck(Application $app): void
    {
        $app->bind(Migrator::class, 'migrator');

        $app->when(DatabaseSchema::class)
            ->needs('$migrationsPath')
            ->give($app->basePath('database/migrations'));
    }

    private function registerHttpEndpoints(
        Application $app,
        RouteRegistrar $registrar
    ): void {
        $app->when(AttachDependencyCheckParameter::class)
            ->needs('$parameter')
            ->give('dependency');

        $endpoints = static function (Router $router): void {
            $router->get('/{dependency}', ShowStatus::class);
        };

        $registrar
            ->middleware(AttachDependencyCheckParameter::class)
            ->prefix('.conductor')
            ->group($endpoints);
    }
}
