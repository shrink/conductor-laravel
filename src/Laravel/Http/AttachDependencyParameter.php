<?php

declare(strict_types=1);

namespace Shrink\Conductor\Laravel\Http;

use Illuminate\Http\Request;
use Shrink\Conductor\Laravel\CollectsApplicationDependencies;
use Symfony\Component\HttpFoundation\Response;

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
        /** @psalm-var \Illuminate\Routing\Route */
        $route = $request->route();

        if (! $route->hasParameter($this->parameter)) {
            /** @psalm-var \Symfony\Component\HttpFoundation\Response */
            return $next($request);
        }

        /** @psalm-var string */
        $id = $route->parameter($this->parameter);

        $dependency = $this->dependencies->dependencyById(
            (string) $id
        );

        $route->setParameter($this->parameter, $dependency);

        /** @psalm-var \Symfony\Component\HttpFoundation\Response */
        return $next($request);
    }
}
