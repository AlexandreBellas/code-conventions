<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules\TestMethodName;

use AlexandreBellas\CodeConventions\PhpStan\Errors;
use AlexandreBellas\CodeConventions\PhpStan\Paths;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * Entity spec methods are named testShould<Action>Successfully.
 *
 * @implements Rule<ClassMethod>
 */
final class TestMethodNameRule implements Rule
{
    public const IDENTIFIER = 'apiLibrary.testMethodName';

    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (!$this->isEntitySpec($scope->getFile()) || !$node->isPublic()) {
            return [];
        }

        $name = $node->name->toString();
        if (!str_starts_with($name, 'test')) {
            return [];
        }

        if (preg_match('/^testShould[A-Z][A-Za-z0-9]*Successfully$/', $name) === 1) {
            return [];
        }

        return [
            Errors::at($node, self::IDENTIFIER, sprintf('%s must be named testShould<Action>Successfully.', $name)),
        ];
    }

    private function isEntitySpec(string $filename): bool
    {
        $located = Paths::entityModule($filename);
        if ($located === null || $located['module'] === '@shared') {
            return false;
        }

        return $located['rest'] === '__tests__/' . $located['module'] . 'Test.spec.php';
    }
}
