<?php

declare(strict_types=1);

namespace Tests\Conductor\Laravel\Unit;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Shrink\Conductor\ChecksDependencyStatus;
use Shrink\Conductor\Laravel\DependenciesArray;
use Shrink\Conductor\Laravel\Dependency;

final class DependenciesArrayTest extends TestCase
{
    /**
     * @test
     */
    public function DependencyIsRegisteredById(): void
    {
        $dependencies = new DependenciesArray(
            $expectedDependency = new Dependency(
                'example',
                $this->createMock(ChecksDependencyStatus::class)
            )
        );

        $dependency = $dependencies->dependencyById('example');

        $this->assertSame($expectedDependency, $dependency);
        $this->assertTrue($dependencies->isDependencyRegistered('example'));
    }

    /**
     * @test
     */
    public function DependencyListIsNotKeyedById(): void
    {
        $check = $this->createMock(ChecksDependencyStatus::class);

        $dependencies = new DependenciesArray(
            $a = new Dependency('a', $check),
            $b = new Dependency('b', $check),
            $c = new Dependency('c', $check),
        );

        $expectedList = [$a, $b, $c];

        $list = $dependencies->listDependencies();

        $this->assertEquals($expectedList, $list);
    }

    /**
     * @test
     */
    public function ErrorIsThrownWhenAskingForUnregisteredDependency(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $dependencies = new DependenciesArray();

        $dependencies->dependencyById('does-not-exist');
    }
}
