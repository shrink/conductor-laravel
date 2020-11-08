<?php

declare(strict_types=1);

namespace Shrink\Conductor\Laravel;

use InvalidArgumentException;

final class DependenciesArray implements CollectsApplicationDependencies
{
    /**
     * @var array<string,\Shrink\Conductor\Laravel\Dependency>
     */
    private array $dependencies = [];

    public function __construct(Dependency ...$dependencies)
    {
        $dependencyById = static function (Dependency $dependency): array {
            return [$dependency->id() => $dependency];
        };

        $keyedDependencies = array_map($dependencyById, $dependencies);

        $this->dependencies = array_merge(...$keyedDependencies);
    }

    /**
     * @return array<\Shrink\Conductor\Laravel\Dependency>
     */
    public function listDependencies(): array
    {
        return array_values($this->dependencies);
    }

    public function isDependencyRegistered(string $id): bool
    {
        return array_key_exists($id, $this->dependencies);
    }

    public function dependencyById(string $id): Dependency
    {
        if (! $this->isDependencyRegistered($id)) {
            throw new InvalidArgumentException(<<<EXCEPTION
            Dependency not found for ID {$id}
            EXCEPTION);
        }

        return $this->dependencies[$id];
    }
}
