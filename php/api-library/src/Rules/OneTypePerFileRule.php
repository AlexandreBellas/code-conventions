<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Ast;
use AlexandreBellas\CodeConventions\PhpStan\Errors;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\Enum_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Trait_;
use PHPStan\Analyser\Scope;
use PHPStan\Node\FileNode;
use PHPStan\Rules\Rule;

/**
 * One class, enum, interface, or trait per file.
 *
 * @implements Rule<FileNode>
 */
final class OneTypePerFileRule implements Rule
{
    public const IDENTIFIER = 'apiLibrary.oneTypePerFile';

    public function getNodeType(): string
    {
        return FileNode::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $types = array_merge(
            Ast::find($node, Class_::class),
            Ast::find($node, Interface_::class),
            Ast::find($node, Trait_::class),
            Ast::find($node, Enum_::class),
        );

        $types = array_values(array_filter(
            $types,
            static fn (ClassLike $type): bool => !$type instanceof Class_ || !$type->isAnonymous(),
        ));

        if (count($types) <= 1) {
            return [];
        }

        return [
            Errors::at($types[1], self::IDENTIFIER, 'A file declares one class, enum, interface, or trait.'),
        ];
    }
}
