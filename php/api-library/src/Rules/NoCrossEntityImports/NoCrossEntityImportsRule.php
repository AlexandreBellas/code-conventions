<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules\NoCrossEntityImports;

use AlexandreBellas\CodeConventions\PhpStan\Ast;
use AlexandreBellas\CodeConventions\PhpStan\Config;
use AlexandreBellas\CodeConventions\PhpStan\Errors;
use AlexandreBellas\CodeConventions\PhpStan\Paths;
use PhpParser\Node;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\GroupUse;
use PhpParser\Node\Stmt\Use_;
use PHPStan\Analyser\Scope;
use PHPStan\Node\FileNode;
use PHPStan\Rules\Rule;

/**
 * An entity module does not import another entity module.
 *
 * @implements Rule<FileNode>
 */
final class NoCrossEntityImportsRule implements Rule
{
    public const IDENTIFIER = 'apiLibrary.noCrossEntityImports';

    public function __construct(private readonly Config $config) {}

    public function getNodeType(): string
    {
        return FileNode::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $file = $scope->getFile();
        $located = Paths::entityModule($file);
        if ($located === null || $located['module'] === '@shared' || Paths::isTest($file)) {
            return [];
        }

        $errors = [];
        foreach (Ast::find($node, Use_::class) as $use) {
            if ($use instanceof GroupUse) {
                continue;
            }

            foreach ($use->uses as $item) {
                $errors = array_merge($errors, $this->check($item, $item->name->toString(), $located['module']));
            }
        }

        foreach (Ast::find($node, GroupUse::class) as $group) {
            foreach ($group->uses as $item) {
                $full = $group->prefix->toString() . '\\' . $item->name->toString();
                $errors = array_merge($errors, $this->check($item, $full, $located['module']));
            }
        }

        foreach (Ast::find($node, FullyQualified::class) as $name) {
            $errors = array_merge($errors, $this->check($name, $name->toString(), $located['module']));
        }

        return $errors;
    }

    /**
     * @return list<\PHPStan\Rules\IdentifierRuleError>
     */
    private function check(Node $node, string $name, string $module): array
    {
        $imported = $this->importedModule($name);
        if ($imported === null || $imported === $module || $imported === 'Shared') {
            return [];
        }

        return [
            Errors::at($node, self::IDENTIFIER, sprintf('This import resolves into entity %s. One resource does not import another. Use this module or Entities\\Shared.', $imported)),
        ];
    }

    private function importedModule(string $name): ?string
    {
        $prefix = trim($this->config->rootNamespace, '\\') . '\\Entities\\';
        if (!str_starts_with($name, $prefix)) {
            return null;
        }

        $rest = substr($name, strlen($prefix));
        $module = strstr($rest, '\\', true);

        return $module === false ? $rest : $module;
    }
}
