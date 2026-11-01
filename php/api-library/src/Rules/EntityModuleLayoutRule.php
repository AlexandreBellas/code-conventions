<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Errors;
use AlexandreBellas\CodeConventions\PhpStan\Paths;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\FileNode;
use PHPStan\Rules\Rule;

/**
 * An entity module is `<Module>.php`, `Enum/`, `Schema/<Operation>/`, and `__tests__/`.
 *
 * @implements Rule<FileNode>
 */
final class EntityModuleLayoutRule implements Rule
{
    public const IDENTIFIER = 'apiLibrary.entityModuleLayout';

    public function getNodeType(): string
    {
        return FileNode::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $located = Paths::entityModule($scope->getFile());
        if ($located === null || $located['module'] === '@shared') {
            return [];
        }

        $rest = $located['rest'];
        $module = $located['module'];
        if (!str_contains($rest, '/')) {
            if ($rest === $module . '.php') {
                return [];
            }

            return [
                Errors::at($node, self::IDENTIFIER, sprintf('The only PHP file in src/Entities/%s/ is %s.php.', $module, $module)),
            ];
        }

        $parts = explode('/', $rest);
        if ($parts[0] === '__tests__') {
            return [];
        }

        if ($parts[0] === 'Enum' && count($parts) === 2 && str_ends_with($parts[1], '.php')) {
            return [];
        }

        if ($parts[0] === 'Schema' && count($parts) === 3 && str_ends_with($parts[2], '.php')) {
            $folder = $parts[1];
            if ($folder === '@shared' || preg_match('/^[A-Z][A-Za-z0-9]*$/', $folder) === 1) {
                return [];
            }

            return [
                Errors::at($node, self::IDENTIFIER, 'A schema operation folder is PascalCase, or @shared when several operations share the type.'),
            ];
        }

        return [
            Errors::at($node, self::IDENTIFIER, 'Entity files live in <Module>.php, Enum/<Name>.php, Schema/<Operation>/<Type>.php, Schema/@shared/<Type>.php, or __tests__/.'),
        ];
    }
}
