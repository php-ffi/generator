<?php

/**
 * This file is part of FFI package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FFI\Generator\Statement;

use FFI\Generator\Statement\Enum\EnumCase;

/**
 * @template-implements \IteratorAggregate<array-key, EnumCase>
 */
class EnumStatement extends Statement implements NamedStatementInterface, \IteratorAggregate, \Countable
{
    /**
     * @var array<EnumCase>
     */
    private array $cases = [];

    /**
     * @param non-empty-string $name
     * @param iterable<int, non-empty-string>|iterable<non-empty-string, int> $cases
     */
    public function __construct(
        private string $name,
        iterable $cases = []
    ) {
        foreach ($cases as $case => $value) {
            // [ int => string ]
            if (\is_int($case)) {
                [$case, $value] = [$value, null];
            }

            /**
             * [ string => int ]
             *
             * @psalm-suppress PossiblyInvalidArgument
             * @psalm-suppress InvalidScalarArgument
             */
            $this->addCase($case, $value);
        }
    }

    /**
     * @param non-empty-string $case
     * @param positive-int|0|null $value
     * @return EnumCase
     */
    public function addCase(string $case, int $value = null): EnumCase
    {
        assert($case !== '', 'Precondition [case != ""] failed');

        if ($value === null) {
            $last = \end($this->cases);
            $value = $last === false ? 0 : $last->getValue() + 1;
        }

        return $this->cases[] = new EnumCase($case, $value);
    }

    /**
     * @param non-empty-string $case
     * @param positive-int|0|null $value
     * @return $this
     * @psalm-mutation-free
     */
    public function withCase(string $case, int $value = null): self
    {
        $self = clone $this;
        /** @psalm-suppress ImpureMethodCall: Mutable call in immutable context */
        $self->addCase($case, $value);

        return $self;
    }

    /**
     * @param non-empty-string $case
     * @return bool
     */
    public function hasCase(string $case): bool
    {
        return isset($this->cases[$case]);
    }

    /**
     * @return array<array-key, EnumCase>
     */
    public function getCases(): array
    {
        return $this->cases;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->cases);
    }

    /**
     * {@inheritDoc}
     */
    public function count(): int
    {
        return \count($this->cases);
    }
}
