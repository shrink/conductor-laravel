<?php

declare(strict_types=1);

namespace Shrink\Conductor\Laravel;

use Shrink\Conductor\ChecksDependencyStatus;

interface CollectsApplicationDependencyChecks
{
    /**
     * @return array<string,\Shrink\Conductor\ChecksDependencyStatus>
     */
    public function listDependencyChecks(): array;

    /**
     * @throws \InvalidArgumentException
     */
    public function dependencyCheckById(string $id): ChecksDependencyStatus;

    public function isDependencyCheckRegistered(string $id): bool;

    /**
     * @throws \InvalidArgumentException
     */
    public function addDependencyCheck(
        string $id,
        ChecksDependencyStatus $check
    ): void;
}
