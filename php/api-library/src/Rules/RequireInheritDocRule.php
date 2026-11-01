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
 * fromResponse and fromRules use @inheritDoc when they implement the base contract.
 *
 * @implements Rule<FileNode>
 */
final class RequireInheritDocRule implements Rule
{
    public const IDENTIFIER = 'apiLibrary.requireInheritDoc';

    public function getNodeType(): string
    {
        return FileNode::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $errors = [];
        foreach (Ast::find($node, Class_::class) as $class) {
            if ($class->isAnonymous()) {
                continue;
            }

            $extends = $class->extends !== null;
            $implementsRoot = $this->implementsResponseRoot($class);
            if (!$extends && !$implementsRoot) {
                continue;
            }

            foreach ($class->getMethods() as $method) {
                $name = $method->name->toString();
                $required = ($name === 'fromRules' && $extends)
                    || ($name === 'fromResponse' && ($extends || $implementsRoot));
                if (!$required || DocComments::hasInheritDoc($method->getDocComment())) {
                    continue;
                }

                $errors[] = Errors::at($method, self::IDENTIFIER, sprintf('%s must document @inheritDoc.', $name));
            }
        }

        return $errors;
    }

    private function implementsResponseRoot(Class_ $class): bool
    {
        foreach ($class->implements as $name) {
            if ($name->getLast() === 'IResponseRootObject') {
                return true;
            }
        }

        return false;
    }
}
