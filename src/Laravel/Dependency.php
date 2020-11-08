<?php

declare(strict_types=1);

namespace Shrink\Conductor\Laravel;

use InvalidArgumentException;
use Shrink\Conductor\ChecksDependencyStatus;
use Shrink\Conductor\DescribesDependencyStatus;
use function preg_match;

final class Dependency
{
    /**
     * An application unique identifier of the Dependency using ascii letters,
     * digits, underscores and hyphens -- up to 32 characters in length.
     */
    private string $id;

    private ChecksDependencyStatus $statusCheck;

    public function __construct(
        string $id,
        ChecksDependencyStatus $statusCheck
    ) {
        if (preg_match('%^[\w-]{1,32}$%', $id) !== 1) {
            throw new InvalidArgumentException(<<<EXCEPTION
            Dependency ID must be 1 to 32 word characters (ASCII letter, digit,
            underscore or hyphen) in length
            EXCEPTION);
        }

        $this->id = $id;
        $this->statusCheck = $statusCheck;
    }

    /**
     * Get the identifier of the Dependency.
     */
    public function id(): string
    {
        return $this->id;
    }

    /**
     * Get the status of the Dependency.
     */
    public function status(): DescribesDependencyStatus
    {
        return $this->statusCheck->dependencyStatus();
    }
}
