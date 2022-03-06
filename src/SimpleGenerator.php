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
use FFI\Generator\Statement\InlineStructStatement;
use FFI\Generator\Statement\InlineUnionStatement;
use FFI\Generator\Statement\NamedStatementInterface;
use FFI\Generator\Statement\RecordStatement;
use FFI\Generator\Statement\StatementInterface;
use FFI\Generator\Statement\StructStatement;
use FFI\Generator\Statement\TypedefStatement;
use FFI\Generator\Statement\UnionStatement;

/**
 * @psalm-suppress LessSpecificReturnStatement
 * @psalm-suppress MoreSpecificReturnType
 */
class SimpleGenerator extends Generator
{
    /**
     * @param DefinitionInterface $definition
     * @return non-empty-string
     */
    public function generate(DefinitionInterface $definition): string
    {
        return match (true) {
            $definition instanceof File => $this->generateFile($definition),
            $definition instanceof TypedefStatement => $this->generateTypedef($definition),
            $definition instanceof StructStatement => $this->generateRootStruct($definition),
            $definition instanceof UnionStatement => $this->generateRootUnion($definition),
            $definition instanceof EnumStatement => $this->generateRootEnum($definition),
            $definition instanceof FunctionStatement => $this->generateRootFunction($definition),
            default => '/* non-renderable type [' . $definition::class . '] */',
        };
    }

    /**
     * @param FunctionStatement $fun
     * @return non-empty-string
     */
    protected function generateRootFunction(FunctionStatement $fun): string
    {
        return \vsprintf('extern %s %s(%s);', [
            $fun->getType(),
            $fun->getName(),
            $this->generateArguments($fun),
        ]);
    }

    /**
     * @param AnonymousFunctionStatement $function
     * @return non-empty-string
     */
    protected function generateArguments(AnonymousFunctionStatement $function): string
    {
        $arguments = [];

        foreach ($function->getArguments() as $arg) {
            $name = $arg->getName();

            $arguments[] = $name !== null
                ? \sprintf('%s %s', $arg->getType(), $name)
                : $arg->getType()
            ;
        }

        if ($arguments === []) {
            $arguments[] = 'void';
        }

        return \implode(', ', $arguments);
    }

    /**
     * @param EnumStatement $enum
     * @return non-empty-string
     */
    protected function generateRootEnum(EnumStatement $enum): string
    {
        if ($enum->count() === 0) {
            return '/* empty enum [' . $enum->getName() . '] */';
        }

        return $this->generate(new TypedefStatement($enum, $enum->getName()));
    }

    /**
     * @param EnumStatement $enum
     * @return non-empty-string
     */
    protected function generateEnum(EnumStatement $enum): string
    {
        if ($enum->count() === 0) {
            return '/* empty enum [' . $enum->getName() . '] */';
        }

        $result = [];

        $result[] = \sprintf('enum %s {', $enum->getName());

        $index = 0;
        foreach ($enum->getCases() as $case) {
            $caseString = \sprintf('%s = %d', $case->getName(), $case->getValue());

            if (++$index !== $enum->count()) {
                $caseString .= ',';
            }

            $result[] = $this->indentString($caseString);
        }

        $result[] = '}';

        return $this->linesToString($result);
    }

    /**
     * @param StructStatement $struct
     * @return non-empty-string
     */
    protected function generateRootStruct(StructStatement $struct): string
    {
        if ($struct->count() === 0) {
            return '/* empty struct [' . $struct->getName() . '] */';
        }

        return $this->generate(new TypedefStatement($struct, $struct->getName()));
    }

    /**
     * @param InlineStructStatement $struct
     * @return non-empty-string
     */
    protected function generateStruct(InlineStructStatement $struct): string
    {
        if ($struct->count() === 0) {
            return 'void* /* empty struct */';
        }

        $result = [];

        $result[] = $struct instanceof NamedStatementInterface
            ? \sprintf('struct %s {', $struct->getName())
            : 'struct {'
        ;

        $result[] = $this->indentString($this->generateRecord($struct));
        $result[] = '}';

        return $this->linesToString($result);
    }

    /**
     * @param UnionStatement $union
     * @return non-empty-string
     */
    protected function generateRootUnion(UnionStatement $union): string
    {
        if ($union->count() === 0) {
            return '/* empty union [' . $union->getName() . '] */';
        }

        return $this->generate(new TypedefStatement($union, $union->getName()));
    }

    /**
     * @param InlineUnionStatement $union
     * @return non-empty-string
     */
    protected function generateUnion(InlineUnionStatement $union): string
    {
        if ($union->count() === 0) {
            return 'void* /* empty union */';
        }

        $result = [];

        $result[] = $union instanceof NamedStatementInterface
            ? \sprintf('union %s {', $union->getName())
            : 'union {'
        ;

        $result[] = $this->indentString($this->generateRecord($union));
        $result[] = '}';

        return $this->linesToString($result);
    }

    /**
     * @param RecordStatement $record
     * @return string
     */
    protected function generateRecord(RecordStatement $record): string
    {
        $result = [];

        foreach ($record->getFields() as $field) {
            $result[] = \vsprintf('%s %s;', [
                $this->generateFieldType($field->getType()),
                $field->getName(),
            ]);
        }

        return $this->linesToString($result);
    }

    /**
     * @param non-empty-string|StatementInterface $type
     * @return non-empty-string
     */
    protected function generateFieldType(string|StatementInterface $type): string
    {
        return match(true) {
            \is_string($type) => $type,
            $type instanceof InlineStructStatement => $this->generateStruct($type),
            $type instanceof InlineUnionStatement => $this->generateUnion($type),
            default => 'void*'
        };
    }

    /**
     * @param File $file
     * @return string
     */
    protected function generateFile(File $file): string
    {
        $result = [];

        foreach ($file->all() as $definition) {
            // Type should be defined as typedef
            $isType = $definition instanceof StructStatement
                || $definition instanceof UnionStatement
                || $definition instanceof EnumStatement
            ;

            if ($isType) {
                $definition = new TypedefStatement($definition, $definition->getName());
            }

            $result[] = $this->generate($definition);
            $result[] = '';
        }

        return $this->linesToString($result);
    }

    /**
     * @param non-empty-string $alias
     * @param AnonymousFunctionStatement $function
     * @return non-empty-string
     */
    protected function generateCallback(string $alias, AnonymousFunctionStatement $function): string
    {
        return \vsprintf('%s (*%s)(%s)', [
            $function->getType(),
            $alias,
            $this->generateArguments($function),
        ]);
    }

    /**
     * @param TypedefStatement $typedef
     * @return non-empty-string
     */
    protected function generateTypedef(TypedefStatement $typedef): string
    {
        if ($typedef->count() === 0) {
            return '/* empty typedef */';
        }

        $type = $typedef->getType();

        if ($type instanceof AnonymousFunctionStatement) {
            $result = [];

            foreach ($typedef->getAliases() as $alias) {
                $result[] = \sprintf('typedef %s;', $this->generateCallback($alias, $type));
            }

            return $this->linesToString($result);
        }

        return \vsprintf('typedef %s %s;', [
            match(true) {
                \is_string($type) => $type,
                $type instanceof StructStatement => $this->generateStruct($type),
                $type instanceof UnionStatement => $this->generateUnion($type),
                $type instanceof EnumStatement => $this->generateEnum($type),
                default => 'void* /* unknown type alias [' . $type::class . '] */',
            },
            \implode(', ', $typedef->getAliases())
        ]);
    }
}
