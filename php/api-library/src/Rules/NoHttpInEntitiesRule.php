<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Ast;
use AlexandreBellas\CodeConventions\PhpStan\Errors;
use AlexandreBellas\CodeConventions\PhpStan\Paths;
use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\GroupUse;
use PhpParser\Node\Stmt\Use_;
use PHPStan\Analyser\Scope;
use PHPStan\Node\FileNode;
use PHPStan\Rules\Rule;

/**
 * Entity files do not construct an HTTP client. Calls go through the repository.
 *
 * @implements Rule<FileNode>
 */
final class NoHttpInEntitiesRule implements Rule
{
    public const IDENTIFIER = 'apiLibrary.noHttpInEntities';

    public function getNodeType(): string
    {
        return FileNode::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $file = $scope->getFile();
        if (Paths::entityModule($file) === null || Paths::isTest($file)) {
            return [];
        }

        $errors = [];
        foreach (Ast::find($node, Use_::class) as $use) {
            foreach ($use->uses as $item) {
                if ($this->isHttpName($item->name->toString())) {
                    $errors[] = $this->error($item);
                }
            }
        }

        foreach (Ast::find($node, GroupUse::class) as $group) {
            foreach ($group->uses as $item) {
                $full = $group->prefix->toString() . '\\' . $item->name->toString();
                if ($this->isHttpName($full)) {
                    $errors[] = $this->error($item);
                }
            }
        }

        foreach (Ast::find($node, FullyQualified::class) as $name) {
            if ($this->isHttpName($name->toString())) {
                $errors[] = $this->error($name);
            }
        }

        foreach (Ast::find($node, FuncCall::class) as $call) {
            if ($call->name instanceof Name && str_starts_with($call->name->toString(), 'curl_')) {
                $errors[] = $this->error($call);
            }
        }

        return $errors;
    }

    private function isHttpName(string $name): bool
    {
        return $name === 'GuzzleHttp' || str_starts_with($name, 'GuzzleHttp\\');
    }

    private function error(Node $node): \PHPStan\Rules\IdentifierRuleError
    {
        return Errors::at($node, self::IDENTIFIER, 'Entity code does not construct an HTTP client. Call $this->repository->index, show, store, update, replace, or destroy.');
    }
}
