<?php

/**
 * This file is part of FFI package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FFI\Generator\Statement\Function;

/**
 * @psalm-internal FFI\Generator
 */
class Argument
{
    /**
     * @param non-empty-string $type
     * @param non-empty-string|null $name
     */
    public function __construct(
        private string $type,
        private ?string $name = null,
    ) {
    }

    /**
     * @return non-empty-string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return non-empty-string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }
}
