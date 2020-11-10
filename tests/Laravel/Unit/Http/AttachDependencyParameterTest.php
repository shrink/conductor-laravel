<?php

declare(strict_types=1);

namespace Tests\Conductor\Laravel\Unit\Http;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Route;
use PHPUnit\Framework\TestCase;
use Shrink\Conductor\ChecksDependencyStatus;
use Shrink\Conductor\Laravel\CollectsApplicationDependencyChecks;
use Shrink\Conductor\Laravel\Http\AttachDependencyCheckParameter;
use StdClass;

final class AttachDependencyCheckParameterTest extends TestCase
{
    /**
     * @test
     */
    public function RouteParameterIsReplacedWithCheckyById(): void
    {
        $check = $this->createMock(ChecksDependencyStatus::class);

        ($checks = $this->createMock(CollectsApplicationDependencyChecks::class))
            ->method('isDependencyCheckRegistered')
            ->with('example-dependency')
            ->willReturn(true);

        $checks
            ->method('dependencyCheckById')
            ->with('example-dependency')
            ->willReturn($check);

        ($route = $this->createMock(Route::class))
            ->method('hasParameter')
            ->with('dependency')
            ->willReturn(true);

        $route
            ->method('parameter')
            ->with('dependency')
            ->willReturn('example-dependency');

        $route
            ->expects($this->once())
            ->method('setParameter')
            ->with('dependency', $check);

        ($request = $this->createMock(Request::class))
            ->expects($this->any())
            ->method('route')
            ->willReturn($route);

        $attachDependencyCheck = new AttachDependencyCheckParameter(
            $checks,
            'dependency'
        );

        $attachDependencyCheck->handle(
            $request,
            fn(Request $request): Response => new Response()
        );
    }

    /**
     * @test
     */
    public function NoActionTakenForRequestWithoutDependencyParameter(): void
    {
        ($checks = $this->createMock(CollectsApplicationDependencyChecks::class))
            ->expects($this->never())
            ->method('dependencyCheckById');

        $attachDependencyCheck = new AttachDependencyCheckParameter(
            $checks,
            'dependency'
        );

        ($route = $this->createMock(Route::class))
            ->method('hasParameter')
            ->with('dependency')
            ->willReturn(false);

        ($request = $this->createMock(Request::class))
            ->expects($this->any())
            ->method('route')
            ->willReturn($route);

        $attachDependencyCheck->handle(
            $request,
            fn(Request $request): Response => new Response()
        );
    }

    /**
     * @test
     */
    public function RequestIsPassedThroughToNextHandler(): void
    {
        ($route = $this->createMock(Route::class))
            ->method('hasParameter')
            ->with('dependency')
            ->willReturn(false);

        ($request = $this->createMock(Request::class))
            ->expects($this->any())
            ->method('route')
            ->willReturn($route);

        $expectedResponse = $this->createMock(Response::class);

        $next = $this->getMockBuilder(StdClass::class)
            ->setMethods(['__invoke'])
            ->getMock();

        $next
            ->expects($this->once())
            ->method('__invoke')
            ->with($request)
            ->willReturn($expectedResponse);

        $attachDependencyCheck = new AttachDependencyCheckParameter(
            $this->createMock(CollectsApplicationDependencyChecks::class),
            'dependency'
        );

        $response = $attachDependencyCheck->handle(
            $request,
            Closure::fromCallable($next)
        );

        $this->assertSame($expectedResponse, $response);
    }
}
