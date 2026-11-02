<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Ast;
use AlexandreBellas\CodeConventions\PhpStan\Config;
use AlexandreBellas\CodeConventions\PhpStan\Errors;
use AlexandreBellas\CodeConventions\PhpStan\Paths;
use PhpParser\Node;
use PhpParser\Node\Stmt\Namespace_;
use PHPStan\Analyser\Scope;
use PHPStan\Node\FileNode;
use PHPStan\Rules\Rule;

/**
 * The namespace mirrors the path under src/, and `@shared` is `Shared`.
 *
 * @implements Rule<FileNode>
 */
final class NamespaceMirrorsPathRule implements Rule
{
    public const IDENTIFIER = 'apiLibrary.namespaceMirrorsPath';

    public function __construct(private readonly Config $config) {}

    public function getNodeType(): string
    {
        return FileNode::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $file = $scope->getFile();
        if (Paths::isTest($file) || Paths::srcRelative($file) === null) {
            return [];
        }

        $expected = Paths::expectedNamespace($file, $this->config->rootNamespace);
        if ($expected === null) {
            return [];
        }

        $namespaces = Ast::find($node, Namespace_::class);
        if (count($namespaces) !== 1 || $namespaces[0]->name === null) {
            return [
                Errors::at($node, self::IDENTIFIER, sprintf('File must declare namespace %s.', $expected)),
            ];
        }

        $actual = $namespaces[0]->name->toString();
        if ($actual === $expected) {
            return [];
        }

        return [
            Errors::at($namespaces[0], self::IDENTIFIER, sprintf('Namespace %s must be %s so it mirrors the path under src/.', $actual, $expected)),
        ];
    }
}
