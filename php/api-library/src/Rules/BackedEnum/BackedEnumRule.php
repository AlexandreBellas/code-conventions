<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules\BackedEnum;

use AlexandreBellas\CodeConventions\PhpStan\Errors;
use AlexandreBellas\CodeConventions\PhpStan\Paths;
use PhpParser\Node;
use PhpParser\Node\Stmt\Enum_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * Enums under Enum/ are backed by string or int.
 *
 * @implements Rule<Enum_>
 */
final class BackedEnumRule implements Rule
{
    public const IDENTIFIER = 'apiLibrary.backedEnum';

    public function getNodeType(): string
    {
        return Enum_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (!Paths::isEnumFile($scope->getFile())) {
            return [];
        }

        $backing = $node->scalarType?->toString();
        if ($backing === 'string' || $backing === 'int') {
            return [];
        }

        $name = $node->name?->toString() ?? 'enum';

        return [
            Errors::at($node, self::IDENTIFIER, sprintf('%s must be a backed enum (`enum %s: string` or `: int`).', $name, $name)),
        ];
    }
}
