<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules\RequireRepositoryInheritDoc;

use AlexandreBellas\CodeConventions\PhpStan\Ast;
use AlexandreBellas\CodeConventions\PhpStan\Config;
use AlexandreBellas\CodeConventions\PhpStan\DocComments;
use AlexandreBellas\CodeConventions\PhpStan\Errors;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Node\FileNode;
use PHPStan\Rules\Rule;

/**
 * A method that implements the configured repository interface uses @inheritDoc.
 *
 * @implements Rule<FileNode>
 */
final class RequireRepositoryInheritDocRule implements Rule
{
    public const IDENTIFIER = 'apiLibrary.requireRepositoryInheritDoc';

    public function __construct(private readonly Config $config) {}

    public function getNodeType(): string
    {
        return FileNode::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $errors = [];
        foreach (Ast::find($node, Class_::class) as $class) {
            if ($class->isAnonymous() || !$this->implementsRepository($class)) {
                continue;
            }

            foreach ($class->getMethods() as $method) {
                $name = $method->name->toString();
                if (!in_array($name, Ast::REPOSITORY_VERBS, true) || DocComments::hasInheritDoc($method->getDocComment())) {
                    continue;
                }

                $errors[] = Errors::at($method, self::IDENTIFIER, sprintf('%s implements %s and must document @inheritDoc.', $name, $this->config->short($this->config->repositoryInterface)));
            }
        }

        return $errors;
    }

    private function implementsRepository(Class_ $class): bool
    {
        $interface = $this->config->short($this->config->repositoryInterface);
        foreach ($class->implements as $name) {
            if ($name->getLast() === $interface) {
                return true;
            }
        }

        return false;
    }
}
