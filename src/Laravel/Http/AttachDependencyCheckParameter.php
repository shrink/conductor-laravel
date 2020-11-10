<?php

declare(strict_types=1);

namespace Shrink\Conductor\Laravel\Http;

use Illuminate\Http\Request;
use Shrink\Conductor\Laravel\CollectsApplicationDependencyChecks;
use Symfony\Component\HttpFoundation\Response;

final class AttachDependencyCheckParameter
{
    private CollectsApplicationDependencyChecks $checks;

    /**
     * Name of the route parameter to attach the check to.
     */
    private string $parameter;

    public function __construct(
        CollectsApplicationDependencyChecks $checks,
        string $parameter
    ) {
        $this->checks = $checks;
        $this->parameter = $parameter;
    }

    /**
     * Find dependency check by identifier from parameter and attach check to
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

        $check = $this->checks->dependencyCheckById(
            (string) $id
        );

        $route->setParameter($this->parameter, $check);

        /** @psalm-var \Symfony\Component\HttpFoundation\Response */
        return $next($request);
    }
}
