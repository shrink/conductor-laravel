<?php

declare(strict_types=1);

namespace Shrink\Conductor\Laravel;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Routing\Router;
use Illuminate\Routing\RouteRegistrar;
use Illuminate\Support\ServiceProvider;
use Lcobucci\Clock\Clock;
use Shrink\Conductor\AggregateDependency;
use Shrink\Conductor\Laravel\Dependencies\DatabaseSchema;
use Shrink\Conductor\Laravel\Http\AttachDependencyCheckParameter;
use Shrink\Conductor\Laravel\Http\ShowStatus;

final class Conductor extends ServiceProvider
{
    public function boot(Application $app, RouteRegistrar $registrar): void
    {
        /** @psalm-var \Lcobucci\Clock\Clock */
        $clock = $app->make(Clock::class);

        $app->instance(
            CollectsApplicationDependencyChecks::class,
            $checks = new DependencyChecksArray()
        );

        $this->registerDatabaseChecks($app, $checks);

        $checks->addDependencyCheck(
            'application',
            new AggregateDependency($clock, $checks->listDependencyChecks())
        );

        $this->registerHttpEndpoints($app, $registrar);
    }

    private function registerDatabaseChecks(
        Application $app,
        CollectsApplicationDependencyChecks $checks
    ): void {
        $app->bind(Migrator::class, 'migrator');

        $app->when(DatabaseSchema::class)
            ->needs('$migrationsPath')
            ->give((string) $app->basePath('database/migrations'));

        /** @psalm-var \Shrink\Conductor\Laravel\Dependencies\DatabaseSchema */
        $databaseSchemaCheck = $app->make(DatabaseSchema::class);

        $checks->addDependencyCheck('schema', $databaseSchemaCheck);
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
