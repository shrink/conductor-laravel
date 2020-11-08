<?php

declare(strict_types=1);

namespace Tests\Conductor\Laravel\Unit\Http;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Route;
use PHPUnit\Framework\TestCase;
use Shrink\Conductor\ChecksDependencyStatus;
use Shrink\Conductor\Laravel\CollectsApplicationDependencies;
use Shrink\Conductor\Laravel\Dependency;
use Shrink\Conductor\Laravel\Http\AttachDependencyParameter;
use StdClass;

final class AttachDependencyParameterTest extends TestCase
{
    /**
     * @test
     */
    public function RouteParameterIsReplacedWithDependencyById(): void
    {
        $dependency = new Dependency(
            'example-dependency',
            $this->createMock(ChecksDependencyStatus::class)
        );

        ($dependencies = $this->createMock(CollectsApplicationDependencies::class))
            ->method('isDependencyRegistered')
            ->with('example-dependency')
            ->willReturn(true);

        $dependencies
            ->method('dependencyById')
            ->with('example-dependency')
            ->willReturn($dependency);

        ($route = $this->createMock(Route::class))
            ->expects($this->once())
            ->method('setParameter')
            ->with('dependency', $dependency);

        ($request = $this->createMock(Request::class))
            ->expects($this->any())
            ->method('get')
            ->with('dependency')
            ->willReturn('example-dependency');

        $request
            ->expects($this->any())
            ->method('route')
            ->willReturn($route);

        $request
            ->expects($this->any())
            ->method('has')
            ->with('dependency')
            ->willReturn(true);

        $attachDependency = new AttachDependencyParameter(
            $dependencies,
            'dependency'
        );

        $attachDependency->handle(
            $request,
            fn(Request $request): Response => new Response()
        );
    }

    /**
     * @test
     */
    public function NoActionTakenForRequestWithoutDependencyParameter(): void
    {
        ($dependencies = $this->createMock(CollectsApplicationDependencies::class))
            ->expects($this->never())
            ->method('dependencyById');

        $attachDependency = new AttachDependencyParameter(
            $dependencies,
            'dependency'
        );

        ($request = $this->createMock(Request::class))
            ->expects($this->any())
            ->method('has')
            ->with('dependency')
            ->willReturn(false);

        $request
            ->expects($this->never())
            ->method('get')
            ->with('dependency');

        $attachDependency->handle(
            $request,
            fn(Request $request): Response => new Response()
        );
    }

    /**
     * @test
     */
    public function RequestIsPassedThroughToNextHandler(): void
    {
        $request = $this->createMock(Request::class);
        $expectedResponse = $this->createMock(Response::class);

        $next = $this->getMockBuilder(StdClass::class)
            ->setMethods(['__invoke'])
            ->getMock();

        $next
            ->expects($this->once())
            ->method('__invoke')
            ->with($request)
            ->willReturn($expectedResponse);

        $attachDependency = new AttachDependencyParameter(
            $this->createMock(CollectsApplicationDependencies::class),
            'dependency'
        );

        $response = $attachDependency->handle(
            $request,
            Closure::fromCallable($next)
        );

        $this->assertSame($expectedResponse, $response);
    }
}
