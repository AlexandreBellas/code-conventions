<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules\NoStrictTypes;

use AlexandreBellas\CodeConventions\PhpStan\Errors;
use PhpParser\Node;
use PhpParser\Node\Stmt\Declare_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;

/**
 * Files do not declare strict_types.
 *
 * @implements Rule<Declare_>
 */
final class NoStrictTypesRule implements Rule
{
    public const IDENTIFIER = 'apiLibrary.noStrictTypes';

    public function getNodeType(): string
    {
        return Declare_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        foreach ($node->declares as $declare) {
            if ($declare->key->toString() !== 'strict_types') {
                continue;
            }

            return [
                Errors::at($node, self::IDENTIFIER, 'Do not declare strict_types. Files start with <?php and a namespace and leave strict_types unset.'),
            ];
        }

        return [];
    }
}
