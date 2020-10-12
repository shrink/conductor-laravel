<?php

declare(strict_types=1);

namespace Shrink\Conductor\Laravel\Dependencies;

use Illuminate\Database\Migrations\Migrator;
use Lcobucci\Clock\Clock;
use Shrink\Conductor\BooleanStatus;
use Shrink\Conductor\ChecksDependencyStatus;
use Shrink\Conductor\DescribesDependencyStatus;

final class DatabaseSchema implements ChecksDependencyStatus
{
    private Migrator $migrator;

    /**
     * Path to the Laravel database migrations for this application relative
     * to the application root, usually that's `database/migrations`.
     *
     * @param string
     */
    private string $migrationsPath;

    private Clock $clock;

    public function __construct(
        Migrator $migrator,
        string $migrationsPath,
        Clock $clock
    ) {
        $this->migrator = $migrator;
        $this->migrationsPath = $migrationsPath;
        $this->clock = $clock;
    }

    /**
     * Check that the database is up to date with the schema expected by the
     * application using the Laravel migrator in `pretend` mode.
     *
     * This check will pass when the database is using the current or future
     * version of the database schema, this check will fail when the database
     * is behind the current schema.
     */
    public function dependencyStatus(): DescribesDependencyStatus
    {
        $migrations = $this->migrator->run(
            [$this->migrationsPath],
            ['pretend' => true]
        );

        $databaseIsMigrated = ($migrations === []);

        return new BooleanStatus(
            $databaseIsMigrated,
            $this->clock->now()
        );
    }
}
