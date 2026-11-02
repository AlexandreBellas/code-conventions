<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules\DoubleQuotedStrings;

use AlexandreBellas\CodeConventions\PhpStan\Errors;
use PhpParser\Node;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * PHP strings use double quotes.
 *
 * @implements Rule<String_>
 */
final class DoubleQuotedStringsRule implements Rule
{
    public const IDENTIFIER = 'apiLibrary.doubleQuotedStrings';

    public function getNodeType(): string
    {
        return String_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $kind = $node->getAttribute('kind');
        if ($kind !== String_::KIND_SINGLE_QUOTED && $kind !== String_::KIND_NOWDOC) {
            return [];
        }

        return [
            Errors::at($node, self::IDENTIFIER, 'PHP strings use double quotes.'),
        ];
    }
}
