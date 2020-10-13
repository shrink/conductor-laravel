<?php

declare(strict_types=1);

namespace Shrink\Conductor\Laravel\Http;

use Illuminate\Http\JsonResponse;
use Shrink\Conductor\Laravel\Dependency;

final class ShowStatus
{
    /**
     * Execute a dependency check and create an HTTP Response using a status
     * code to represent the state of the check.
     */
    public function __invoke(Dependency $dependency): JsonResponse
    {
        $code = $dependency->status()->hasStatusCheckPassed() ? 200 : 503;

        return new JsonResponse([], $code);
    }
}
