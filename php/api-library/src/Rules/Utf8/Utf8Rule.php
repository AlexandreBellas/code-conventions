<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules\Utf8;

use AlexandreBellas\CodeConventions\PhpStan\Errors;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\FileNode;
use PHPStan\Rules\Rule;

/**
 * Source files are UTF-8.
 *
 * @implements Rule<FileNode>
 */
final class Utf8Rule implements Rule
{
    public const IDENTIFIER = 'apiLibrary.utf8';

    public function getNodeType(): string
    {
        return FileNode::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $contents = file_get_contents($scope->getFile());
        if ($contents === false || mb_check_encoding($contents, 'UTF-8')) {
            return [];
        }

        return [
            Errors::at($node, self::IDENTIFIER, 'Files are UTF-8.'),
        ];
    }
}
