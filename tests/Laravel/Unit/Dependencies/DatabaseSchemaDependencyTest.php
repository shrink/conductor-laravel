<?php

declare(strict_types=1);

namespace Tests\Conductor\Laravel\Unit\Dependencies;

use DateTimeImmutable;
use Illuminate\Database\Migrations\Migrator;
use Lcobucci\Clock\Clock;
use PHPUnit\Framework\TestCase;
use Shrink\Conductor\BooleanStatus;
use Shrink\Conductor\Laravel\Dependencies\DatabaseSchema;

final class DatabaseSchemaTest extends TestCase
{
    /**
     * @test
     */
    public function StatusCheckFailsWhenDatabaseHasPendingMigrations(): void
    {
        ($migrator = $this->createMock(Migrator::class))
            ->method('run')
            ->willReturn([
                'example_migration_1.php',
                'example_migration_2.php',
            ]);

        $databaseSchema = new DatabaseSchema(
            $migrator,
            'database/migrations',
            $this->createMock(Clock::class)
        );

        $status = $databaseSchema->dependencyStatus();

        $this->assertFalse($status->hasStatusCheckPassed());
    }

    /**
     * @test
     */
    public function StatusCheckPassesWhenDatabaseIsUpToDate(): void
    {
        ($migrator = $this->createMock(Migrator::class))
            ->method('run')
            ->willReturn([]);

        $databaseSchema = new DatabaseSchema(
            $migrator,
            'database/migrations',
            $this->createMock(Clock::class)
        );

        $status = $databaseSchema->dependencyStatus();

        $this->assertTrue($status->hasStatusCheckPassed());
    }

    /**
     * @test
     */
    public function MigratorIsToldToPretendToExecuteMigrations(): void
    {
        ($migrator = $this->createMock(Migrator::class))
            ->expects($this->atLeastOnce())
            ->method('run')
            ->with($this->anything(), ['pretend' => true])
            ->willReturn([]);

        $databaseSchema = new DatabaseSchema(
            $migrator,
            'database/migrations',
            $this->createMock(Clock::class)
        );

        $databaseSchema->dependencyStatus();
    }

    /**
     * @test
     */
    public function MigratorIsToldToLookForMigrationsInPath(): void
    {
        ($migrator = $this->createMock(Migrator::class))
            ->expects($this->atLeastOnce())
            ->method('run')
            ->with(['database/migrations'], $this->anything())
            ->willReturn([]);

        $databaseSchema = new DatabaseSchema(
            $migrator,
            'database/migrations',
            $this->createMock(Clock::class)
        );

        $databaseSchema->dependencyStatus();
    }

    /**
     * @test
     */
    public function StatusIsCheckedAtCurrentTime(): void
    {
        ($migrator = $this->createMock(Migrator::class))
            ->method('run')
            ->willReturn([]);

        ($clock = $this->createMock(Clock::class))
            ->method('now')
            ->willReturn($at = new DateTimeImmutable('2018-01-01 00:00:00'));

        $databaseSchema = new DatabaseSchema(
            $migrator,
            'database/migrations',
            $clock
        );

        $status = $databaseSchema->dependencyStatus();

        $this->assertEquals($at, $status->statusCheckedAt());
    }
}
