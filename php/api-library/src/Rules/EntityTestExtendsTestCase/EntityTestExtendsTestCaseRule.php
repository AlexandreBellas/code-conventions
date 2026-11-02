<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules\EntityTestExtendsTestCase;

use AlexandreBellas\CodeConventions\PhpStan\Errors;
use AlexandreBellas\CodeConventions\PhpStan\Paths;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * An entity spec extends PHPUnit\Framework\TestCase.
 *
 * @implements Rule<Class_>
 */
final class EntityTestExtendsTestCaseRule implements Rule
{
    public const IDENTIFIER = 'apiLibrary.entityTestExtendsTestCase';

    public function getNodeType(): string
    {
        return Class_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if ($node->isAnonymous() || !$this->isEntitySpec($scope->getFile())) {
            return [];
        }

        if ($node->extends !== null && $node->extends->getLast() === 'TestCase') {
            return [];
        }

        return [
            Errors::at($node, self::IDENTIFIER, 'An entity spec extends PHPUnit\\Framework\\TestCase.'),
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
