<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Errors;
use AlexandreBellas\CodeConventions\PhpStan\Paths;
use PhpParser\Node;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * Entities do not set the Authorization header. The repository sends the bearer token.
 *
 * @implements Rule<String_>
 */
final class NoAuthorizationInEntitiesRule implements Rule
{
    public const IDENTIFIER = 'apiLibrary.noAuthorizationInEntities';

    public function getNodeType(): string
    {
        return String_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $file = $scope->getFile();
        if (Paths::entityModule($file) === null || Paths::isTest($file)) {
            return [];
        }

        if ($node->value !== 'Authorization' && !str_starts_with($node->value, 'Authorization:')) {
            return [];
        }

        return [
            Errors::at($node, self::IDENTIFIER, 'Entities do not set Authorization. The bearer header is set in the repository.'),
        ];
    }
}
