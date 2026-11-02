<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules\RequireEntitySpec;

use AlexandreBellas\CodeConventions\PhpStan\Errors;
use AlexandreBellas\CodeConventions\PhpStan\Paths;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\FileNode;
use PHPStan\Rules\Rule;

/**
 * Each entity module has `__tests__/<Module>Test.spec.php`.
 *
 * @implements Rule<FileNode>
 */
final class RequireEntitySpecRule implements Rule
{
    public const IDENTIFIER = 'apiLibrary.requireEntitySpec';

    public function getNodeType(): string
    {
        return FileNode::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $file = $scope->getFile();
        if (!Paths::isEntityModuleClass($file)) {
            return [];
        }

        $located = Paths::entityModule($file);
        $directory = Paths::moduleDirectory($file);
        if ($located === null || $directory === null) {
            return [];
        }

        $spec = $directory . '/__tests__/' . $located['module'] . 'Test.spec.php';
        if (is_file($spec)) {
            return [];
        }

        return [
            Errors::at($node, self::IDENTIFIER, sprintf('%s has no __tests__/%sTest.spec.php.', $located['module'], $located['module'])),
        ];
    }
}
