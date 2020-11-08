<?php

declare(strict_types=1);

namespace Tests\Conductor\Laravel\Unit;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Shrink\Conductor\Laravel\Dependency;
use Shrink\Conductor\ChecksDependencyStatus;
use Shrink\Conductor\DescribesDependencyStatus;
use function str_repeat;

final class DependencyTest extends TestCase
{
    /**
     * @test
     */
    public function DependencyHasId(): void
    {
        $dependency = new Dependency(
            'example',
            $this->createMock(ChecksDependencyStatus::class)
        );

        $this->assertSame('example', $dependency->id());
    }

    /**
     * @test
     */
    public function DependencyHasStatusFromCheck(): void
    {
        $status = $this->createMock(DescribesDependencyStatus::class);

        ($statusCheck = $this->createMock(ChecksDependencyStatus::class))
            ->method('dependencyStatus')
            ->willReturn($status);

        $dependency = new Dependency('example', $statusCheck);

        $this->assertSame($status, $dependency->status());
    }

    /**
     * Create a list of invalid dependency IDs.
     *
     * @return array<string<array<string>>>
     */
    public function invalidDependencyIds(): array
    {
        return [
            'Empty string' => [''],
            '33 characters in length' => [str_repeat('a', 33)],
            'Contains invalid characters' => ['hello(<invalid>)world'],
        ];
    }

    /**
     * @test
     * @dataProvider invalidDependencyIds
     */
    public function DependencyIdIsNotValid(string $id): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Dependency(
            $id,
            $this->createMock(ChecksDependencyStatus::class)
        );
    }

    /**
     * Create a list of valid dependency IDs.
     *
     * @return array<string<array<string>>>
     */
    public function validDependencyIds(): array
    {
        return [
            'Single character' => ['a'],
            '32 characters in length' => [str_repeat('a', 32)],
            'Contains underscore and hyphens' => ['a_b-c_d'],
        ];
    }

    /**
     * @test
     * @dataProvider validDependencyIds
     */
    public function DependencyIdIsValid(string $id): void
    {
        $dependency = new Dependency(
            $id,
            $this->createMock(ChecksDependencyStatus::class)
        );

        $this->assertSame($id, $dependency->id());
    }
}
