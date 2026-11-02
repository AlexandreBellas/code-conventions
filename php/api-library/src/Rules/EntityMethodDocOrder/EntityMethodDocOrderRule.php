<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules\EntityMethodDocOrder;

use AlexandreBellas\CodeConventions\PhpStan\DocComments;
use AlexandreBellas\CodeConventions\PhpStan\Errors;
use AlexandreBellas\CodeConventions\PhpStan\Paths;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * Public entity method docblocks use description, @param, @return, @throws, @see.
 *
 * @implements Rule<ClassMethod>
 */
final class EntityMethodDocOrderRule implements Rule
{
    public const IDENTIFIER = 'apiLibrary.entityMethodDocOrder';

    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (!Paths::isEntityModuleClass($scope->getFile()) || !$node->isPublic() || $node->name->toString() === '__construct') {
            return [];
        }

        $violation = DocComments::methodOrderViolation($node->getDocComment());
        if ($violation === null) {
            return [];
        }

        return [
            Errors::at($node, self::IDENTIFIER, $violation),
        ];
    }
}
