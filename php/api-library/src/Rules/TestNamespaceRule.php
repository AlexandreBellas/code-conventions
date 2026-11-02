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
 * Specs use `Tests\Unit\` plus the production namespace, without `__tests__`.
 *
 * @implements Rule<FileNode>
 */
final class TestNamespaceRule implements Rule
{
    public const IDENTIFIER = 'apiLibrary.testNamespace';

    public function __construct(private readonly Config $config) {}

    public function getNodeType(): string
    {
        return FileNode::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $file = $scope->getFile();
        if (!Paths::isTest($file)) {
            return [];
        }

        $expected = Paths::expectedTestNamespace($file, $this->config->rootNamespace);
        if ($expected === null) {
            return [];
        }

        $namespaces = Ast::find($node, Namespace_::class);
        $actual = (count($namespaces) === 1 && $namespaces[0]->name !== null) ? $namespaces[0]->name->toString() : null;
        if ($actual === $expected) {
            return [];
        }

        $target = $namespaces[0] ?? $node;

        return [
            Errors::at($target, self::IDENTIFIER, sprintf('Test namespace must be %s.', $expected)),
        ];
    }
}
