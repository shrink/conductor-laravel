<?php

declare(strict_types=1);

namespace Tests\Conductor\Laravel\Unit\Http;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Shrink\Conductor\ChecksDependencyStatus;
use Shrink\Conductor\DescribesDependencyStatus;
use Shrink\Conductor\Laravel\CollectsApplicationDependencyChecks;
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

        $pass
            ->method('statusCheckedAt')
            ->willReturn(new DateTimeImmutable('2018-01-01 00:00:00'));

        ($passingCheck = $this->createMock(ChecksDependencyStatus::class))
            ->method('dependencyStatus')
            ->willReturn($pass);

        $expectedResponse = json_encode([
            'status' => 'pass',
            'checkedAt' => '2018-01-01T00:00:00+00:00'
        ]);

        $response = (new ShowStatus())->__invoke($passingCheck);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertEquals($expectedResponse, $response->getContent());
    }

    /**
     * @test
     */
    public function FailingStatusCheckReturns503Response(): void
    {
        ($fail = $this->createMock(DescribesDependencyStatus::class))
            ->method('hasStatusCheckPassed')
            ->willReturn(false);

        $fail
            ->method('statusCheckedAt')
            ->willReturn(new DateTimeImmutable('2018-01-01 00:00:00'));

        ($failingCheck = $this->createMock(ChecksDependencyStatus::class))
            ->method('dependencyStatus')
            ->willReturn($fail);

        $expectedResponse = json_encode([
            'status' => 'fail',
            'checkedAt' => '2018-01-01T00:00:00+00:00'
        ]);

        $response = (new ShowStatus())->__invoke($failingCheck);

        $this->assertSame(503, $response->getStatusCode());
        $this->assertEquals($expectedResponse, $response->getContent());
    }
}
