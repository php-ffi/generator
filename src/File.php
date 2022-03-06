<?php

/**
 * This file is part of FFI package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FFI\Generator;

use FFI\Generator\Statement\AnonymousFunctionStatement;
use FFI\Generator\Statement\EnumStatement;
use FFI\Generator\Statement\FunctionStatement;
use FFI\Generator\Statement\StatementInterface;
use FFI\Generator\Statement\StructStatement;
use FFI\Generator\Statement\TypedefStatement;
use FFI\Generator\Statement\UnionStatement;

/**
 * @template-implements \IteratorAggregate<array-key, StatementInterface>
 */
class File extends Definition implements \IteratorAggregate, \Countable
{
    /**
     * @var array<StatementInterface>
     */
    protected array $statements;

    /**
     * @param iterable<StatementInterface> $statements
     */
    public function __construct(
        iterable $statements = [],
    ) {
        if ($statements instanceof \Traversable) {
            $statements = \iterator_to_array($statements, false);
        }

        $this->statements = $statements;
    }

    /**
     * @param non-empty-string|StatementInterface $type
     * @param non-empty-string|array<non-empty-string> $alias
     * @return TypedefStatement
     */
    public function addTypedef(string|StatementInterface $type, string|iterable $alias = []): TypedefStatement
    {
        $this->add($result = new TypedefStatement($type, $alias));

        return $result;
    }

    /**
     * @param non-empty-string|StatementInterface $type
     * @param non-empty-string|array<non-empty-string> $alias
     * @return self
     * @psalm-mutation-free
     */
    public function withTypedef(string|StatementInterface $type, string|iterable $alias): self
    {
        $self = clone $this;
        /** @psalm-suppress ImpureMethodCall: Mutable call in immutable context */
        $self->addTypedef($type, $alias);

        return $self;
    }

    /**
     * @return iterable<TypedefStatement>
     */
    public function getTypedefs(): iterable
    {
        return $this->filter(TypedefStatement::class);
    }

    /**
     * @param non-empty-string $name
     * @param iterable<int, non-empty-string>|iterable<non-empty-string, int> $cases
     * @return EnumStatement
     */
    public function addEnum(string $name, iterable $cases = []): EnumStatement
    {
        $this->add($enum = new EnumStatement($name, $cases));

        return $enum;
    }

    /**
     * @param non-empty-string $name
     * @param iterable<int, non-empty-string>|iterable<non-empty-string, int> $cases
     * @return self
     * @psalm-mutation-free
     */
    public function withEnum(string $name, iterable $cases = []): self
    {
        $self = clone $this;
        /** @psalm-suppress ImpureMethodCall: Mutable call in immutable context */
        $self->addEnum($name, $cases);

        return $self;
    }

    /**
     * @return iterable<EnumStatement>
     */
    public function getEnums(): iterable
    {
        return $this->filter(EnumStatement::class);
    }

    /**
     * @param non-empty-string $name
     * @param iterable<non-empty-string, non-empty-string|StatementInterface> $fields
     * @return StructStatement
     */
    public function addStruct(string $name, iterable $fields = []): StructStatement
    {
        $this->add($struct = new StructStatement($name, $fields));

        return $struct;
    }

    /**
     * @param non-empty-string $name
     * @param iterable<non-empty-string, non-empty-string|StatementInterface> $fields
     * @return self
     * @psalm-mutation-free
     */
    public function withStruct(string $name, iterable $fields = []): self
    {
        $self = clone $this;
        /** @psalm-suppress ImpureMethodCall: Mutable call in immutable context */
        $self->addStruct($name, $fields);

        return $self;
    }

    /**
     * @return iterable<StructStatement>
     */
    public function getStructs(): iterable
    {
        return $this->filter(StructStatement::class);
    }

    /**
     * @param non-empty-string $name
     * @param iterable<non-empty-string, non-empty-string|StatementInterface> $fields
     * @return UnionStatement
     */
    public function addUnion(string $name, iterable $fields = []): UnionStatement
    {
        $this->add($union = new UnionStatement($name, $fields));

        return $union;
    }

    /**
     * @param non-empty-string $name
     * @param iterable<non-empty-string, non-empty-string|StatementInterface> $fields
     * @return self
     * @psalm-mutation-free
     */
    public function withUnion(string $name, iterable $fields = []): self
    {
        $self = clone $this;
        /** @psalm-suppress ImpureMethodCall: Mutable call in immutable context */
        $self->addUnion($name, $fields);

        return $self;
    }

    /**
     * @return iterable<UnionStatement>
     */
    public function getUnions(): iterable
    {
        return $this->filter(UnionStatement::class);
    }

    /**
     * @param non-empty-string $name
     * @param non-empty-string $type
     * @param iterable<non-empty-string, non-empty-string>|list<non-empty-string> $arguments
     * @return AnonymousFunctionStatement
     */
    public function addCallback(string $name, string $type = 'void', iterable $arguments = []): AnonymousFunctionStatement
    {
        $function = new AnonymousFunctionStatement($type, $arguments);

        $this->add(new TypedefStatement($function, $name));

        return $function;
    }

    /**
     * @param non-empty-string $name
     * @param non-empty-string $type
     * @param iterable<non-empty-string, non-empty-string>|list<non-empty-string> $arguments
     * @return self
     * @psalm-mutation-free
     */
    public function withCallback(string $name, string $type = 'void', iterable $arguments = []): self
    {
        $self = clone $this;
        /** @psalm-suppress ImpureMethodCall: Mutable call in immutable context */
        $self->addCallback($name, $type, $arguments);

        return $self;
    }

    /**
     * @return iterable<non-empty-string, AnonymousFunctionStatement>
     */
    public function getCallbacks(): iterable
    {
        /** @var TypedefStatement $typedef */
        foreach ($this->filter(TypedefStatement::class) as $typedef) {
            $type = $typedef->getType();

            if ($type instanceof AnonymousFunctionStatement) {
                foreach ($typedef->getAliases() as $alias) {
                    yield $alias => $type;
                }
            }
        }
    }

    /**
     * @param non-empty-string $name
     * @param non-empty-string $type
     * @param iterable<non-empty-string, non-empty-string>|list<non-empty-string> $arguments
     * @return FunctionStatement
     */
    public function addFunction(string $name, string $type = 'void', iterable $arguments = []): FunctionStatement
    {
        $this->add($function = new FunctionStatement($name, $type, $arguments));

        return $function;
    }

    /**
     * @param non-empty-string $name
     * @param non-empty-string $type
     * @param iterable<non-empty-string, non-empty-string>|list<non-empty-string> $arguments
     * @return self
     * @psalm-mutation-free
     */
    public function withFunction(string $name, string $type = 'void', iterable $arguments = []): self
    {
        $self = clone $this;
        /** @psalm-suppress ImpureMethodCall: Mutable call in immutable context */
        $self->addFunction($name, $type, $arguments);

        return $self;
    }

    /**
     * @return iterable<FunctionStatement>
     */
    public function getFunctions(): iterable
    {
        return $this->filter(FunctionStatement::class);
    }

    /**
     * @param StatementInterface $stmt
     * @return bool
     */
    public function has(StatementInterface $stmt): bool
    {
        return \in_array($stmt, $this->statements, true);
    }

    /**
     * @param StatementInterface ...$stmt
     * @return void
     */
    public function add(StatementInterface ...$stmt): void
    {
        foreach ($stmt as $item) {
            $this->statements[] = $item;
        }
    }

    /**
     * @param StatementInterface ...$stmt
     * @return $this
     * @psalm-mutation-free
     */
    public function with(StatementInterface ...$stmt): self
    {
        $self = clone $this;
        /** @psalm-suppress ImpureMethodCall: Mutable call in immutable context */
        $self->add(...$stmt);

        return $self;
    }

    /**
     * @return array<StatementInterface>
     */
    public function all(): array
    {
        return $this->statements;
    }

    /**
     * @template T of StatementInterface
     * @param class-string<T> $type
     * @return iterable<T>
     */
    private function filter(string $type): iterable
    {
        foreach ($this as $statement) {
            if ($statement instanceof $type) {
                yield $statement;
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->statements);
    }

    /**
     * {@inheritDoc}
     */
    public function count(): int
    {
        return \count($this->statements);
    }
}
