<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules\RequireEntityReturnDoc;

use AlexandreBellas\CodeConventions\PhpStan\DocComments;
use AlexandreBellas\CodeConventions\PhpStan\Errors;
use AlexandreBellas\CodeConventions\PhpStan\Paths;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * Each public entity method documents @return.
 *
 * @implements Rule<ClassMethod>
 */
final class RequireEntityReturnDocRule implements Rule
{
    public const IDENTIFIER = 'apiLibrary.requireEntityReturnDoc';

    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (!Paths::isEntityModuleClass($scope->getFile()) || !$node->isPublic() || $node->name->toString() === '__construct') {
            return [];
        }

        if (DocComments::hasReturn($node->getDocComment())) {
            return [];
        }

        return [
            Errors::at($node, self::IDENTIFIER, sprintf('%s must document @return.', $node->name->toString())),
        ];
    }
}
