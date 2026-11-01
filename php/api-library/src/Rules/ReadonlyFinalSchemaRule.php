<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Errors;
use AlexandreBellas\CodeConventions\PhpStan\Paths;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * Schema classes are `readonly final class`.
 *
 * @implements Rule<Class_>
 */
final class ReadonlyFinalSchemaRule implements Rule
{
    public const IDENTIFIER = 'apiLibrary.readonlyFinalSchema';

    public function getNodeType(): string
    {
        return Class_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if ($node->isAnonymous() || !Paths::isSchema($scope->getFile())) {
            return [];
        }

        if ($node->isReadonly() && $node->isFinal()) {
            return [];
        }

        $name = $node->name?->toString() ?? 'schema class';

        return [
            Errors::at($node, self::IDENTIFIER, sprintf('Schema class %s must be declared as `readonly final class`.', $name)),
        ];
    }
}
