<?php

/**string
 * This file is part of FFI package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FFI\Generator\Statement\Record;

use FFI\Generator\Statement\StatementInterface;

/**
 * @psalm-internal FFI\Generator
 */
class Field
{
    /**
     * @param non-empty-string $name
     * @param non-empty-string|StatementInterface $type
     */
    public function __construct(
        private string $name,
        private string|StatementInterface $type,
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
     * @return StatementInterface|non-empty-string
     */
    public function getType(): string|StatementInterface
    {
        return $this->type;
    }
}
