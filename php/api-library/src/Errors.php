<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan;

use PhpParser\Node;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\RuleErrorBuilder;

final class Errors
{
    public static function at(Node $node, string $identifier, string $message): IdentifierRuleError
    {
        return RuleErrorBuilder::message($message)
            ->identifier($identifier)
            ->line($node->getStartLine())
            ->build();
    }
}
