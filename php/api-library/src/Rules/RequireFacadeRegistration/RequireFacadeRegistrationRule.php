<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules\RequireFacadeRegistration;

use AlexandreBellas\CodeConventions\PhpStan\Config;
use AlexandreBellas\CodeConventions\PhpStan\Errors;
use AlexandreBellas\CodeConventions\PhpStan\Paths;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\FileNode;
use PHPStan\Rules\Rule;

/**
 * The entity is imported, documented with @property, and constructed via getModule() in the facade file.
 *
 * @implements Rule<FileNode>
 */
final class RequireFacadeRegistrationRule implements Rule
{
    public const IDENTIFIER = 'apiLibrary.requireFacadeRegistration';

    /** @var array<string, string|null> */
    private static array $facades = [];

    public function __construct(private readonly Config $config) {}

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
        $facadeClass = $this->config->short($this->config->facadeClass);
        $facade = Paths::facadeFile($file, $facadeClass);
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
                Errors::at($node, self::IDENTIFIER, sprintf('%s is not registered because src/%s.php is missing.', $class, $facadeClass)),
            ];
        }

        $errors = [];
        $facadeFile = 'src/' . $facadeClass . '.php';
        $import = trim($this->config->rootNamespace, '\\') . '\\Entities\\' . $class . '\\' . $class;
        if (preg_match('/use\s+' . preg_quote($import, '/') . '\s*;/', $source) !== 1) {
            $errors[] = Errors::at($node, self::IDENTIFIER, sprintf('%s is missing `use %s;` in %s.', $class, $import, $facadeFile));
        }

        if (preg_match('/@property\s+' . preg_quote($class, '/') . '\s+\$' . preg_quote($getter, '/') . '\b/', $source) !== 1) {
            $errors[] = Errors::at($node, self::IDENTIFIER, sprintf('%s is missing `@property %s $%s` in %s.', $class, $class, $getter, $facadeFile));
        }

        if (preg_match('/getModule\(\s*' . preg_quote($class, '/') . '::class\s*\)/', $source) !== 1) {
            $errors[] = Errors::at($node, self::IDENTIFIER, sprintf('%s is missing `getModule(%s::class)` in %s.', $class, $class, $facadeFile));
        }

        return $errors;
    }
}
