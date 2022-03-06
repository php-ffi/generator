<?php

/**
 * This file is part of FFI package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FFI\Generator\Statement;

use FFI\Generator\Statement\Record\Field;

/**
 * @template-implements \IteratorAggregate<array-key, Field>
 */
abstract class RecordStatement extends Statement implements \IteratorAggregate, \Countable
{
    /**
     * @var list<Field>
     */
    private array $fields = [];

    /**
     * @param iterable<non-empty-string, non-empty-string|StatementInterface> $fields
     */
    public function __construct(
        iterable $fields = [],
    ) {
        foreach ($fields as $name => $type) {
            $this->addField($name, $type);
        }
    }

    /**
     * @param non-empty-string $name
     * @param non-empty-string|StatementInterface $type
     * @return Field
     */
    public function addField(string $name, string|StatementInterface $type): Field
    {
        assert($name !== '', 'Precondition [name != ""] failed');

        return $this->fields[] = new Field($name, $type);
    }

    /**
     * @param non-empty-string $name
     * @param non-empty-string|StatementInterface $type
     * @return self
     * @psalm-mutation-free
     */
    public function withField(string $name, string|StatementInterface $type): self
    {
        $self = clone $this;
        /** @psalm-suppress ImpureMethodCall: Mutable call in immutable context */
        $self->addField($name, $type);

        return $self;
    }

    /**
     * @param non-empty-string $name
     * @return bool
     */
    public function hasField(string $name): bool
    {
        foreach ($this->fields as $field) {
            if ($field->getName() === $name) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<array-key, Field>
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->fields);
    }

    /**
     * {@inheritDoc}
     */
    public function count(): int
    {
        return \count($this->fields);
    }
}
