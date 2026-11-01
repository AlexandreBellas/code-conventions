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
 * The entity is imported, documented with @property, and constructed via getModule() in src/Bling.php.
 *
 * @implements Rule<FileNode>
 */
final class RequireFacadeRegistrationRule implements Rule
{
    public const IDENTIFIER = 'apiLibrary.requireFacadeRegistration';

    /** @var array<string, string|null> */
    private static array $facades = [];

    public function getNodeType(): string
    {
        return FileNode::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $file = $scope->getFile();
        if (!Paths::isEntityModuleClass($file)) {
            return [];
        }

        $located = Paths::entityModule($file);
        if ($located === null) {
            return [];
        }

        $class = $located['module'];
        $getter = lcfirst($class);
        $facade = Paths::facadeFile($file);
        if ($facade === null) {
            return [];
        }

        if (!array_key_exists($facade, self::$facades)) {
            self::$facades[$facade] = is_file($facade) ? file_get_contents($facade) : null;
            if (self::$facades[$facade] === false) {
                self::$facades[$facade] = null;
            }
        }

        $source = self::$facades[$facade];
        if ($source === null) {
            return [
                Errors::at($node, self::IDENTIFIER, sprintf('%s is not registered because src/Bling.php is missing.', $class)),
            ];
        }

        $errors = [];
        $import = Paths::NAMESPACE_ROOT . '\\Entities\\' . $class . '\\' . $class;
        if (preg_match('/use\s+' . preg_quote($import, '/') . '\s*;/', $source) !== 1) {
            $errors[] = Errors::at($node, self::IDENTIFIER, sprintf('%s is missing `use %s;` in src/Bling.php.', $class, $import));
        }

        if (preg_match('/@property\s+' . preg_quote($class, '/') . '\s+\$' . preg_quote($getter, '/') . '\b/', $source) !== 1) {
            $errors[] = Errors::at($node, self::IDENTIFIER, sprintf('%s is missing `@property %s $%s` in src/Bling.php.', $class, $class, $getter));
        }

        if (preg_match('/getModule\(\s*' . preg_quote($class, '/') . '::class\s*\)/', $source) !== 1) {
            $errors[] = Errors::at($node, self::IDENTIFIER, sprintf('%s is missing `getModule(%s::class)` in src/Bling.php.', $class, $class));
        }

        return $errors;
    }
}
