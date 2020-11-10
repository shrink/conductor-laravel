<?php

declare(strict_types=1);

namespace Shrink\Conductor\Laravel\Http;

use DateTimeInterface;
use Illuminate\Http\JsonResponse;
use Shrink\Conductor\ChecksDependencyStatus;

final class ShowStatus
{
    /**
     * Execute a dependency check and create an HTTP Response using a status
     * code to represent the status of the check.
     */
    public function __invoke(ChecksDependencyStatus $check): JsonResponse
    {
        $status = $check->dependencyStatus();

        $description = [
            'status' => $status->hasStatusCheckPassed() ? 'pass' : 'fail',
            'checkedAt' => $status
                ->statusCheckedAt()
                ->format(DateTimeInterface::ATOM),
        ];

        return new JsonResponse(
            $description,
            $status->hasStatusCheckPassed() ? 200 : 503
        );
    }
}
