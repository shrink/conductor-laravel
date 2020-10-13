<?php

declare(strict_types=1);

namespace Shrink\Conductor\Laravel;

interface CollectsApplicationDependencies
{
    /**
     * @return array<\Shrink\Conductor\Laravel\Dependency>
     */
    public function listDependencies(): array;

    /**
     * @throws \InvalidArgumentException
     */
    public function dependencyById(string $id): Dependency;

    public function isDependencyRegistered(string $id): bool;
}
