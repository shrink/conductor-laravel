<?php

declare(strict_types=1);

namespace Shrink\Conductor\Laravel\Http;

use Illuminate\Http\JsonResponse;
use Shrink\Conductor\Laravel\CollectsApplicationDependencies;
use Shrink\Conductor\Laravel\Dependency;

final class ShowStatus
{
    private CollectsApplicationDependencies $dependencies;

    public function __construct(CollectsApplicationDependencies $dependencies)
    {
        $this->dependencies = $dependencies;
    }

    /**
     * Execute a dependency check and create an HTTP Response using a status
     * code to represent the state of the check.
     */
    public function __invoke(string $id): JsonResponse
    {
        if (! $this->dependencies->isDependencyRegistered($id)) {
            return new JsonResponse([], 404);
        }

        return $this->statusResponse($this->dependencies->dependencyById($id));
    }

    private function statusResponse(Dependency $dependency): JsonResponse
    {
        $code = $dependency->status()->hasStatusCheckPassed() ? 200 : 503;

        return new JsonResponse([], $code);
    }
}
