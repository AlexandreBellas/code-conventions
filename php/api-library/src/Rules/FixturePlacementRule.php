<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Errors;
use AlexandreBellas\CodeConventions\PhpStan\Paths;
use FilesystemIterator;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\FileNode;
use PHPStan\Rules\Rule;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * JSON fixtures stay in `__tests__/`, and `__tests__/` does not hold extra PHP types.
 *
 * @implements Rule<FileNode>
 */
final class FixturePlacementRule implements Rule
{
    public const IDENTIFIER = 'apiLibrary.fixturePlacement';

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

        $directory = Paths::moduleDirectory($file);
        if ($directory === null) {
            return [];
        }

        $errors = [];
        $schema = $directory . '/Schema';
        if (is_dir($schema)) {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($schema, FilesystemIterator::SKIP_DOTS));
            foreach ($iterator as $item) {
                if ($item->isFile() && str_ends_with(strtolower($item->getFilename()), '.json')) {
                    $errors[] = Errors::at($node, self::IDENTIFIER, sprintf('JSON fixture %s does not belong in Schema/. Put it under __tests__/.', $this->relative($directory, $item->getPathname())));
                }
            }
        }

        $tests = $directory . '/__tests__';
        if (is_dir($tests)) {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($tests, FilesystemIterator::SKIP_DOTS));
            foreach ($iterator as $item) {
                if (!$item->isFile() || !str_ends_with($item->getFilename(), '.php')) {
                    continue;
                }

                if (str_ends_with($item->getFilename(), 'Test.spec.php')) {
                    continue;
                }

                $errors[] = Errors::at($node, self::IDENTIFIER, sprintf('PHP file %s does not belong in __tests__/. Schema classes stay in Schema/.', $this->relative($directory, $item->getPathname())));
            }
        }

        return $errors;
    }

    private function relative(string $moduleDirectory, string $path): string
    {
        $path = Paths::normalize($path);
        $prefix = Paths::normalize($moduleDirectory) . '/';

        return str_starts_with($path, $prefix) ? substr($path, strlen($prefix)) : $path;
    }
}
