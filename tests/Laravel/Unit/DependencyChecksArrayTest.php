<?php

declare(strict_types=1);

namespace Tests\Conductor\Laravel\Unit;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Shrink\Conductor\ChecksDependencyStatus;
use Shrink\Conductor\DescribesDependencyStatus;
use Shrink\Conductor\Laravel\DependencyChecksArray;

final class DependencyChecksArrayTest extends TestCase
{
    /**
     * @test
     */
    public function CheckIsRegisteredById(): void
    {
        $checks = new DependencyChecksArray();

        $checks->addDependencyCheck(
            'example-check',
            $expectedCheck = $this->createMock(ChecksDependencyStatus::class)
        );

        $check = $checks->dependencyCheckById('example-check');

        $this->assertSame($expectedCheck, $check);
        $this->assertTrue($checks->isDependencyCheckRegistered('example-check'));
    }

    /**
     * @test
     */
    public function ErrorIsThrownWhenAskingForUnregisteredDependency(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $checks = new DependencyChecksArray();

        $checks->dependencyCheckById('does-not-exist');
    }

    /**
     * @test
     */
    public function AllRegisteredDependencyChecksAreRegistered(): void
    {
        $checks = new DependencyChecksArray();
        $check = $this->createMock(ChecksDependencyStatus::class);

        $checks->addDependencyCheck('example-check-1', $check);
        $checks->addDependencyCheck('example-check-3', $check);
        $checks->addDependencyCheck('example-check-2', $check);

        $expectedChecks = [
            'example-check-1' => $check,
            'example-check-3' => $check,
            'example-check-2' => $check,
        ];

        $this->assertEquals($expectedChecks, $checks->listDependencyChecks());
    }
}
