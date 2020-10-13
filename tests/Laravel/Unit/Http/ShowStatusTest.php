<?php

declare(strict_types=1);

namespace Tests\Conductor\Laravel\Unit\Http;

use PHPUnit\Framework\TestCase;
use Shrink\Conductor\ChecksDependencyStatus;
use Shrink\Conductor\DescribesDependencyStatus;
use Shrink\Conductor\Laravel\CollectsApplicationDependencies;
use Shrink\Conductor\Laravel\Dependency;
use Shrink\Conductor\Laravel\Http\ShowStatus;

final class ShowStatusTest extends TestCase
{
    /**
     * @test
     */
    public function PassingStatusCheckReturns200Response(): void
    {
        ($pass = $this->createMock(DescribesDependencyStatus::class))
            ->method('hasStatusCheckPassed')
            ->willReturn(true);

        ($passingCheck = $this->createMock(ChecksDependencyStatus::class))
            ->method('dependencyStatus')
            ->willReturn($pass);

        $dependency = new Dependency('passing', $passingCheck);

        $response = (new ShowStatus())->__invoke($dependency);

        $this->assertSame(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function FailingStatusCheckReturns503Response(): void
    {
        ($fail = $this->createMock(DescribesDependencyStatus::class))
            ->method('hasStatusCheckPassed')
            ->willReturn(false);

        ($failingCheck = $this->createMock(ChecksDependencyStatus::class))
            ->method('dependencyStatus')
            ->willReturn($fail);

        $dependency = new Dependency('failing', $failingCheck);

        $response = (new ShowStatus())->__invoke($dependency);

        $this->assertSame(503, $response->getStatusCode());
    }
}
