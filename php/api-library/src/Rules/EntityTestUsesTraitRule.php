<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Config;
use AlexandreBellas\CodeConventions\PhpStan\Errors;
use AlexandreBellas\CodeConventions\PhpStan\Paths;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\TraitUse;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * An entity spec uses the configured response trait.
 *
 * @implements Rule<Class_>
 */
final class EntityTestUsesTraitRule implements Rule
{
    public const IDENTIFIER = 'apiLibrary.entityTestUsesTrait';

    public function __construct(private readonly Config $config) {}

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
                if ($trait->getLast() === $this->config->short($this->config->testResponseTrait)) {
                    return [];
                }
            }
        }

        return [
            Errors::at($node, self::IDENTIFIER, sprintf('An entity spec uses %s.', $this->config->short($this->config->testResponseTrait))),
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
