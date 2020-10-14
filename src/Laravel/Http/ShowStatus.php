<?php

declare(strict_types=1);

namespace Shrink\Conductor\Laravel\Http;

use DateTimeInterface;
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
        $status = $dependency->status();

        $results = [
            1 => ['pass', 200],
            0 => ['fail', 503],
        ];

        [$label, $code] = $results[(int) $status->hasStatusCheckPassed()];

        $description = [
            'dependency' => $dependency->id(),
            'status' => $label,
            'checkedAt' => $status
                ->statusCheckedAt()
                ->format(DateTimeInterface::ATOM),
        ];

        return new JsonResponse($description, $code);
    }
}
