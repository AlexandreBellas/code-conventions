<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Ast;
use AlexandreBellas\CodeConventions\PhpStan\Errors;
use AlexandreBellas\CodeConventions\PhpStan\Paths;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Node\FileNode;
use PHPStan\Rules\Rule;

/**
 * The entity module class is a concrete, non-readonly subclass of BaseEntity.
 *
 * @implements Rule<FileNode>
 */
final class EntityExtendsBaseRule implements Rule
{
    public const IDENTIFIER = 'apiLibrary.entityExtendsBase';

    public function getNodeType(): string
    {
        return FileNode::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (!Paths::isEntityModuleClass($scope->getFile())) {
            return [];
        }

        $classes = array_values(array_filter(
            Ast::find($node, Class_::class),
            static fn (Class_ $class): bool => !$class->isAnonymous(),
        ));

        $class = $classes[0] ?? null;
        $extendsBase = $class?->extends !== null && $class->extends->getLast() === 'BaseEntity';
        if ($class !== null && $extendsBase && !$class->isAbstract() && !$class->isReadonly()) {
            return [];
        }

        $located = Paths::entityModule($scope->getFile());
        $name = $located['module'] ?? 'Entity';

        return [
            Errors::at($class ?? $node, self::IDENTIFIER, sprintf('Entity class %s must be a concrete, non-readonly subclass of BaseEntity.', $name)),
        ];
    }
}
