<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules\RequireGetInstance;

use AlexandreBellas\CodeConventions\PhpStan\Config;
use AlexandreBellas\CodeConventions\PhpStan\Errors;
use AlexandreBellas\CodeConventions\PhpStan\Paths;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * An entity spec defines private getInstance().
 *
 * @implements Rule<Class_>
 */
final class RequireGetInstanceRule implements Rule
{
    public const IDENTIFIER = 'apiLibrary.requireGetInstance';

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

        foreach ($node->getMethods() as $method) {
            if ($method->name->toString() === 'getInstance' && $method->isPrivate()) {
                return [];
            }
        }

        return [
            Errors::at($node, self::IDENTIFIER, sprintf('An entity spec defines private function getInstance(%s $repository).', $this->config->short($this->config->repositoryInterface))),
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
