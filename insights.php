<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Import\OrderedImportsFixer;

return [
    'config' => [
        OrderedImportsFixer::class => [
            'imports_order' => ['class', 'const', 'function'],
            'sort_algorithm' => 'alpha',
        ],
    ],
    'requirements' => [
        'min-architecture' => 100,
        'min-complexity' => 90,
        'min-quality' => 100,
        'min-style' => 100,
    ],
];
