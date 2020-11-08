<?php

declare(strict_types=1);

namespace Shrink\Conductor\Laravel\Http;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Shrink\Conductor\Laravel\CollectsApplicationDependencies;

final class AttachDependencyParameter
{
    private CollectsApplicationDependencies $dependencies;

    /**
     * Name of the route parameter to attach the dependency to.
     */
    private string $parameter;

    public function __construct(
        CollectsApplicationDependencies $dependencies,
        string $parameter
    ) {
        $this->dependencies = $dependencies;
        $this->parameter = $parameter;
    }

    /**
     * Find dependency by identifier from parameter and attach dependency to
     * route as a parameter.
     */
    public function handle(Request $request, callable $next): Response
    {
        if (! $request->has($this->parameter)) {
            /** @psalm-var \Illuminate\Http\Response */
            return $next($request);
        }

        $dependency = $this->dependencies->dependencyById(
            (string) $request->get($this->parameter)
        );

        /** @psalm-var \Illuminate\Routing\Route */
        $route = $request->route();

        $route->setParameter($this->parameter, $dependency);

        /** @psalm-var \Illuminate\Http\Response */
        return $next($request);
    }
}
