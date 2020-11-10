<?php

declare(strict_types=1);

namespace Shrink\Conductor\Laravel;

use InvalidArgumentException;
use Shrink\Conductor\ChecksDependencyStatus;
use function array_key_exists;
use function preg_match;

final class DependencyChecksArray implements CollectsApplicationDependencyChecks
{
    /**
     * @var array<string,\Shrink\Conductor\ChecksDependencyStatus>
     */
    private array $checks = [];

    /**
     * @return array<string,\Shrink\Conductor\ChecksDependencyStatus>
     */
    public function listDependencyChecks(): array
    {
        return $this->checks;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function dependencyCheckById(string $id): ChecksDependencyStatus
    {
        if (! $this->isDependencyCheckRegistered($id)) {
            throw new InvalidArgumentException(<<<EXCEPTION
            Dependency check not found for ID {$id}
            EXCEPTION);
        }

        return $this->checks[$id];
    }

    public function isDependencyCheckRegistered(string $id): bool
    {
        return array_key_exists($id, $this->checks);
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function addDependencyCheck(
        string $id,
        ChecksDependencyStatus $check
    ): void {
        if (preg_match('%^[\w-]{1,32}$%', $id) !== 1) {
            throw new InvalidArgumentException(<<<EXCEPTION
            Dependency ID must be 1 to 32 word characters (ASCII letter, digit,
            underscore or hyphen) in length
            EXCEPTION);
        }

        $this->checks[$id] = $check;
    }
}
