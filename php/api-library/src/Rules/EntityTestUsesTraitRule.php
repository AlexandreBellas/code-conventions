<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Errors;
use AlexandreBellas\CodeConventions\PhpStan\Paths;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\TraitUse;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * An entity spec uses TestResponseTrait.
 *
 * @implements Rule<Class_>
 */
final class EntityTestUsesTraitRule implements Rule
{
    public const IDENTIFIER = 'apiLibrary.entityTestUsesTrait';

    public function getNodeType(): string
    {
        return Class_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if ($node->isAnonymous() || !$this->isEntitySpec($scope->getFile())) {
            return [];
        }

        foreach ($node->stmts as $statement) {
            if (!$statement instanceof TraitUse) {
                continue;
            }

            foreach ($statement->traits as $trait) {
                if ($trait->getLast() === 'TestResponseTrait') {
                    return [];
                }
            }
        }

        return [
            Errors::at($node, self::IDENTIFIER, 'An entity spec uses TestResponseTrait.'),
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
