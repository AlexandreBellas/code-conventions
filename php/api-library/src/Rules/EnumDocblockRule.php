<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Errors;
use AlexandreBellas\CodeConventions\PhpStan\Paths;
use PhpParser\Node;
use PhpParser\Node\Stmt\Enum_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * An enum docblock starts with "Enumerador de".
 *
 * @implements Rule<Enum_>
 */
final class EnumDocblockRule implements Rule
{
    public const IDENTIFIER = 'apiLibrary.enumDocblock';

    public function getNodeType(): string
    {
        return Enum_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (!Paths::isEnumFile($scope->getFile())) {
            return [];
        }

        $text = $node->getDocComment()?->getText() ?? '';
        if (str_contains($text, 'Enumerador de')) {
            return [];
        }

        $name = $node->name?->toString() ?? 'enum';

        return [
            Errors::at($node, self::IDENTIFIER, sprintf('%s must start its docblock with "Enumerador de".', $name)),
        ];
    }
}
