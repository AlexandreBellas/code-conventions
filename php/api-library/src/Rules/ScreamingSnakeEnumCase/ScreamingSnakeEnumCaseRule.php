<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules\ScreamingSnakeEnumCase;

use AlexandreBellas\CodeConventions\PhpStan\Errors;
use AlexandreBellas\CodeConventions\PhpStan\Paths;
use PhpParser\Node;
use PhpParser\Node\Stmt\EnumCase;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * Enum case names are SCREAMING_SNAKE.
 *
 * @implements Rule<EnumCase>
 */
final class ScreamingSnakeEnumCaseRule implements Rule
{
    public const IDENTIFIER = 'apiLibrary.screamingSnakeEnumCase';

    public function getNodeType(): string
    {
        return EnumCase::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (!Paths::isEnumFile($scope->getFile())) {
            return [];
        }

        $name = $node->name->toString();
        if (preg_match('/^[A-Z][A-Z0-9]*(_[A-Z0-9]+)*$/', $name) === 1) {
            return [];
        }

        return [
            Errors::at($node, self::IDENTIFIER, sprintf('Enum case %s must be SCREAMING_SNAKE.', $name)),
        ];
    }
}
