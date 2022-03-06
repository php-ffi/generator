<?php

/**
 * This file is part of FFI package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FFI\Generator\Statement\Enum;

/**
 * @psalm-internal FFI\Generator
 */
class EnumCase
{
    /**
     * @param non-empty-string $name
     * @param positive-int|0 $value
     */
    public function __construct(
        private string $name,
        private int $value,
    ) {
    }

    /**
     * @return non-empty-string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return positive-int|0
     */
    public function getValue(): int
    {
        return $this->value;
    }
}
