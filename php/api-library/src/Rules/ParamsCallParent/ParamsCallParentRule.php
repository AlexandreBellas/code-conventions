<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules\ParamsCallParent;

use AlexandreBellas\CodeConventions\PhpStan\Ast;
use AlexandreBellas\CodeConventions\PhpStan\Config;
use AlexandreBellas\CodeConventions\PhpStan\Errors;
use AlexandreBellas\CodeConventions\PhpStan\Paths;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PHPStan\Analyser\Scope;
use PHPStan\Node\FileNode;
use PHPStan\Rules\Rule;

/**
 * A *Params constructor finishes with parent::__construct(toArrayFunction($this)).
 *
 * @implements Rule<FileNode>
 */
final class ParamsCallParentRule implements Rule
{
    public const IDENTIFIER = 'apiLibrary.paramsCallParent';

    public function __construct(private readonly Config $config) {}

    public function getNodeType(): string
    {
        return FileNode::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (!Paths::isSchema($scope->getFile())) {
            return [];
        }

        $errors = [];
        foreach (Ast::find($node, Class_::class) as $class) {
            $className = $class->name?->toString();
            if ($className === null || !str_ends_with($className, 'Params')) {
                continue;
            }

            foreach ($class->getMethods() as $method) {
                if ($method->name->toString() !== '__construct') {
                    continue;
                }

                if ($this->finishesWithParent($method)) {
                    continue;
                }

                $errors[] = Errors::at($method, self::IDENTIFIER, sprintf('%s::__construct must finish with parent::__construct(%s($this)).', $className, $this->config->toArrayFunction));
            }
        }

        return $errors;
    }

    private function finishesWithParent(ClassMethod $method): bool
    {
        $statements = $method->stmts ?? [];
        if ($statements === []) {
            return false;
        }

        $last = $statements[array_key_last($statements)];
        $expr = $last instanceof Expression ? $last->expr : $last;

        return Ast::isParentToArrayCall($expr, $this->config->toArrayFunction);
    }
}
