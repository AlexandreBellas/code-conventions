<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules;

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
 * `@shared` and Helpers do not import an entity module.
 *
 * @implements Rule<FileNode>
 */
final class SharedDoesNotImportEntitiesRule implements Rule
{
    public const IDENTIFIER = 'apiLibrary.sharedDoesNotImportEntities';

    public function __construct(private readonly Config $config) {}

    public function getNodeType(): string
    {
        return FileNode::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (!Paths::isSharedOrHelpers($scope->getFile()) || Paths::isTest($scope->getFile())) {
            return [];
        }

        $names = [];
        foreach (Ast::find($node, Use_::class) as $use) {
            if ($use instanceof GroupUse) {
                continue;
            }

            foreach ($use->uses as $item) {
                $names[] = [$item, $item->name->toString()];
            }
        }

        foreach (Ast::find($node, GroupUse::class) as $group) {
            foreach ($group->uses as $item) {
                $names[] = [$item, $group->prefix->toString() . '\\' . $item->name->toString()];
            }
        }

        foreach (Ast::find($node, FullyQualified::class) as $name) {
            $names[] = [$name, $name->toString()];
        }

        $errors = [];
        $prefix = trim($this->config->rootNamespace, '\\') . '\\Entities\\';
        foreach ($names as [$target, $name]) {
            if (!str_starts_with($name, $prefix)) {
                continue;
            }

            $rest = substr($name, strlen($prefix));
            $module = strstr($rest, '\\', true);
            $module = $module === false ? $rest : $module;
            if ($module === 'Shared') {
                continue;
            }

            $errors[] = Errors::at($target, self::IDENTIFIER, sprintf('@shared and Helpers do not import entity %s.', $module));
        }

        return $errors;
    }
}
