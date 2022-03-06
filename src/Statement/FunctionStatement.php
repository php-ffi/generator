<?php

/**
 * This file is part of FFI package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FFI\Generator\Statement;

class FunctionStatement extends AnonymousFunctionStatement implements NamedStatementInterface
{
    /**
     * @param non-empty-string $name
     * @param non-empty-string $type
     * @param iterable<non-empty-string, non-empty-string>|list<non-empty-string> $arguments
     */
    public function __construct(
        private string $name,
        string $type = 'void',
        iterable $arguments = [],
    ) {
        parent::__construct($type, $arguments);
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return $this->name;
    }
}
