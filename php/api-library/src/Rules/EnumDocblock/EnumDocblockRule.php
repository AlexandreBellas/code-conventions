<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules\EnumDocblock;

use AlexandreBellas\CodeConventions\PhpStan\Config;
use AlexandreBellas\CodeConventions\PhpStan\Errors;
use AlexandreBellas\CodeConventions\PhpStan\Paths;
use PhpParser\Node;
use PhpParser\Node\Stmt\Enum_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * An enum docblock contains the configured prefix.
 *
 * @implements Rule<Enum_>
 */
final class EnumDocblockRule implements Rule
{
    public const IDENTIFIER = 'apiLibrary.enumDocblock';

    public function __construct(private readonly Config $config) {}

    public function getNodeType(): string
    {
        return Enum_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (!Paths::isEnumFile($scope->getFile())) {
            return [];
        }

        $prefix = $this->config->enumDocblockPrefix;
        $text = $node->getDocComment()?->getText() ?? '';
        if (str_contains($text, $prefix)) {
            return [];
        }

        $name = $node->name?->toString() ?? 'enum';

        return [
            Errors::at($node, self::IDENTIFIER, sprintf('%s must start its docblock with "%s".', $name, $prefix)),
        ];
    }
}
