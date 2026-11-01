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
 * QueryParams, Body, and Headers are `readonly class` and stay open for extension.
 *
 * @implements Rule<Class_>
 */
final class ReadonlyRequestDtoRule implements Rule
{
    public const IDENTIFIER = 'apiLibrary.readonlyRequestDto';

    public function getNodeType(): string
    {
        return Class_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $name = $node->name?->toString();
        if ($name === null || $node->isAnonymous() || !Paths::isRequestDto($scope->getFile())) {
            return [];
        }

        if (!in_array($name, ['QueryParams', 'Body', 'Headers'], true)) {
            return [];
        }

        if ($node->isReadonly() && !$node->isFinal()) {
            return [];
        }

        return [
            Errors::at($node, self::IDENTIFIER, sprintf('%s must be a non-final `readonly class`.', $name)),
        ];
    }
}
