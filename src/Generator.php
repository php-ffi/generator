<?php

/**
 * This file is part of FFI package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FFI\Generator;

abstract class Generator implements GeneratorInterface
{
    /**
     * @param non-empty-string $delimiter
     * @param non-empty-string $indent
     */
    public function __construct(
        protected string $delimiter = "\n",
        protected string $indent = '    ',
    ) {
    }

    /**
     * @param list<string> $lines
     * @return string
     */
    protected function linesToString(iterable $lines): string
    {
        if ($lines instanceof \Traversable) {
            /** @psalm-suppress MixedArgument */
            $lines = \iterator_to_array($lines, false);
        }

        /** @psalm-suppress MixedArgumentTypeCoercion */
        return \implode($this->delimiter, $lines);
    }

    /**
     * @param list<string> $lines
     * @param positive-int|0 $indent
     * @return list<string>
     *
     * @psalm-suppress InvalidReturnType
     */
    protected function indentLines(iterable $lines, int $indent = 1): iterable
    {
        $prefix = \str_repeat($this->indent, $indent);

        foreach ($lines as $line) {
            yield $prefix . $line;
        }
    }

    /**
     * @param string $text
     * @param positive-int|0 $indent
     * @return string
     */
    protected function indentString(string $text, int $indent = 1): string
    {
        $lines = \explode($this->delimiter, $text);

        return $this->linesToString(
            $this->indentLines($lines, $indent)
        );
    }
}
