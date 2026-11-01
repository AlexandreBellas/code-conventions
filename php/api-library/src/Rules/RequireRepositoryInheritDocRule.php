<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Ast;
use AlexandreBellas\CodeConventions\PhpStan\DocComments;
use AlexandreBellas\CodeConventions\PhpStan\Errors;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Node\FileNode;
use PHPStan\Rules\Rule;

/**
 * A method that implements IBlingRepository uses @inheritDoc.
 *
 * @implements Rule<FileNode>
 */
final class RequireRepositoryInheritDocRule implements Rule
{
    public const IDENTIFIER = 'apiLibrary.requireRepositoryInheritDoc';

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

                $errors[] = Errors::at($method, self::IDENTIFIER, sprintf('%s implements IBlingRepository and must document @inheritDoc.', $name));
            }
        }

        return $errors;
    }

    private function implementsRepository(Class_ $class): bool
    {
        foreach ($class->implements as $name) {
            if ($name->getLast() === 'IBlingRepository') {
                return true;
            }
        }

        if ($class->extends !== null && $class->extends->getLast() === 'BlingRepository') {
            return true;
        }

        return false;
    }
}
