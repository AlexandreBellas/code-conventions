<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules\EntityMethodsArePublic;

use AlexandreBellas\CodeConventions\PhpStan\Errors;
use AlexandreBellas\CodeConventions\PhpStan\Paths;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * Entity module methods are public.
 *
 * @implements Rule<ClassMethod>
 */
final class EntityMethodsArePublicRule implements Rule
{
    public const IDENTIFIER = 'apiLibrary.entityMethodsArePublic';

    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (!Paths::isEntityModuleClass($scope->getFile()) || $node->isPublic()) {
            return [];
        }

        return [
            Errors::at($node, self::IDENTIFIER, sprintf('Entity method %s must be public.', $node->name->toString())),
        ];
    }
}
