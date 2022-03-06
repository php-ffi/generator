<?php

/**
 * This file is part of FFI package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FFI\Generator\Statement;

/**
 * @template-implements \IteratorAggregate<array-key, non-empty-string>
 */
class TypedefStatement extends Statement implements \IteratorAggregate, \Countable
{
    /**
     * @var list<non-empty-string>
     */
    private array $aliases = [];

    /**
     * @param non-empty-string|StatementInterface $type
     * @param non-empty-string|iterable<non-empty-string> $alias
     */
    public function __construct(
        private string|StatementInterface $type,
        string|iterable $alias = [],
    ) {
        assert($this->type !== '', 'Precondition [type != ""] failed');

        foreach ((\is_string($alias) ? [$alias] : $alias) as $current) {
            assert($current !== '', 'Precondition [alias != ""] failed');
            $this->aliases[] = $current;
        }

        assert($this->aliases !== [], 'Precondition [alias != []] failed');
    }

    /**
     * @param non-empty-string ...$alias
     * @return void
     */
    public function addAlias(string ...$alias): void
    {
        foreach ($alias as $item) {
            $this->aliases[] = $item;
        }
    }

    /**
     * @param non-empty-string ...$alias
     * @return self
     * @psalm-mutation-free
     */
    public function withAlias(string ...$alias): self
    {
        $self = clone $this;
        /** @psalm-suppress ImpureMethodCall: Mutable call in immutable context */
        $self->addAlias(...$alias);

        return $self;
    }

    /**
     * @return non-empty-string|StatementInterface
     */
    public function getType(): string|StatementInterface
    {
        return $this->type;
    }

    /**
     * @return array<non-empty-string>
     */
    public function getAliases(): array
    {
        return $this->aliases;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->aliases);
    }

    /**
     * {@inheritDoc}
     */
    public function count(): int
    {
        return \count($this->aliases);
    }
}
