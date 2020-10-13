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

        ($dependencies = $this->createMock(CollectsApplicationDependencies::class))
            ->method('dependencyById')
            ->with('passing')
            ->willReturn(new Dependency('passing', $passingCheck));

        $dependencies
            ->method('isDependencyRegistered')
            ->with('passing')
            ->willReturn(true);

        $showStatus = new ShowStatus($dependencies);

        $response = $showStatus->__invoke('passing');

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

        ($dependencies = $this->createMock(CollectsApplicationDependencies::class))
            ->method('dependencyById')
            ->with('failing')
            ->willReturn(new Dependency('failing', $failingCheck));

        $dependencies
            ->method('isDependencyRegistered')
            ->with('failing')
            ->willReturn(true);

        $showStatus = new ShowStatus($dependencies);

        $response = $showStatus->__invoke('failing');

        $this->assertSame(503, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function UnregisteredDependencyIdReturns404(): void
    {
        ($dependencies = $this->createMock(CollectsApplicationDependencies::class))
            ->method('isDependencyRegistered')
            ->with('dependency-not-registered')
            ->willReturn(false);

        $showStatus = new ShowStatus($dependencies);

        $response = $showStatus->__invoke('dependency-not-registered');

        $this->assertSame(404, $response->getStatusCode());
    }
}
