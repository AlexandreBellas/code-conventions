<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules\NoBracketQueryNames;

use AlexandreBellas\CodeConventions\PhpStan\Ast;
use AlexandreBellas\CodeConventions\PhpStan\Errors;
use PhpParser\Node;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Node\FileNode;
use PHPStan\Rules\Rule;

/**
 * Query names do not already end with [].
 *
 * @implements Rule<FileNode>
 */
final class NoBracketQueryNamesRule implements Rule
{
    public const IDENTIFIER = 'apiLibrary.noBracketQueryNames';

    public function getNodeType(): string
    {
        return FileNode::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $errors = [];
        foreach (Ast::find($node, ArrayItem::class) as $item) {
            if (!$item->key instanceof String_ || !str_ends_with($item->key->value, '[]')) {
                continue;
            }

            $errors[] = Errors::at(
                $item->key,
                self::IDENTIFIER,
                sprintf('Query name "%s" already ends with []. Leave the PHP name without brackets.', $item->key->value),
            );
        }

        return $errors;
    }
}
