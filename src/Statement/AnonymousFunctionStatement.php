<?php

/**
 * This file is part of FFI package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FFI\Generator\Statement;

use FFI\Generator\Statement\Function\Argument;

class AnonymousFunctionStatement extends Statement implements \IteratorAggregate, \Countable
{
    /**
     * @var array<Argument>
     */
    private array $arguments = [];

    /**
     * @param non-empty-string $type
     * @param iterable<non-empty-string, non-empty-string>|list<non-empty-string> $arguments
     */
    public function __construct(
        private string $type = 'void',
        iterable $arguments = [],
    ) {
        foreach ($arguments as $name => $type) {
            if (\is_int($name)) {
                $name = null;
            }

            $this->addArgument($type, $name);
        }
    }

    /**
     * @return non-empty-string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param non-empty-string $type
     * @param non-empty-string|null $name
     * @return Argument
     */
    public function addArgument(string $type, string $name = null): Argument
    {
        return $this->arguments[] = new Argument($type, $name);
    }

    /**
     * @param non-empty-string $type
     * @param non-empty-string|null $name
     * @return $this
     * @psalm-mutation-free
     */
    public function withArgument(string $type, string $name = null): self
    {
        $self = clone $this;
        /** @psalm-suppress ImpureMethodCall: Mutable call in immutable context */
        $self->addArgument($type, $name);

        return $self;
    }

    /**
     * @param non-empty-string $name
     * @return bool
     */
    public function hasArgument(string $name): bool
    {
        foreach ($this->arguments as $argument) {
            if ($argument->getName() === $name) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<Argument>
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->arguments);
    }

    /**
     * {@inheritDoc}
     */
    public function count(): int
    {
        return \count($this->arguments);
    }
}
