<?php

/**
 * This file is part of FFI package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FFI\Generator\Statement;

class StructStatement extends InlineStructStatement implements NamedStatementInterface
{
    /**
     * @param non-empty-string $name
     * @param iterable<non-empty-string, non-empty-string|StatementInterface> $fields
     */
    public function __construct(
        private string $name,
        iterable $fields = []
    ) {
        parent::__construct($fields);
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return $this->name;
    }
}
