<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules\FileNameMatchesType;

use AlexandreBellas\CodeConventions\PhpStan\Ast;
use AlexandreBellas\CodeConventions\PhpStan\Errors;
use AlexandreBellas\CodeConventions\PhpStan\Paths;
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
 * The file stem is the type name. `Foo.spec.php` declares `Foo`.
 *
 * @implements Rule<FileNode>
 */
final class FileNameMatchesTypeRule implements Rule
{
    public const IDENTIFIER = 'apiLibrary.fileNameMatchesType';

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
            static fn (ClassLike $type): bool => $type->name !== null && (!$type instanceof Class_ || !$type->isAnonymous()),
        ));

        if (count($types) !== 1) {
            return [];
        }

        $actual = $types[0]->name->toString();
        $expected = Paths::typeStem($scope->getFile());
        if ($actual === $expected) {
            return [];
        }

        return [
            Errors::at(
                $types[0],
                self::IDENTIFIER,
                sprintf('File %s must declare %s so the file name matches the type name.', basename($scope->getFile()), $expected),
            ),
        ];
    }
}
