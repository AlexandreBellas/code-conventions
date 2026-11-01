<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Ast;
use AlexandreBellas\CodeConventions\PhpStan\Errors;
use AlexandreBellas\CodeConventions\PhpStan\Paths;
use PhpParser\Node;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\If_;
use PHPStan\Analyser\Scope;
use PHPStan\Node\FileNode;
use PHPStan\Rules\Rule;

/**
 * Every helper in functions.php is wrapped in function_exists().
 *
 * @implements Rule<FileNode>
 */
final class FunctionExistsWrapperRule implements Rule
{
    public const IDENTIFIER = 'apiLibrary.functionExistsWrapper';

    public function getNodeType(): string
    {
        return FileNode::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (!Paths::isHelpersFunctions($scope->getFile())) {
            return [];
        }

        $wrapped = [];
        foreach (Ast::find($node, If_::class) as $if) {
            $condition = $if->cond instanceof BooleanNot ? $if->cond->expr : $if->cond;
            $isGuard = $condition instanceof FuncCall
                && $condition->name instanceof Name
                && $condition->name->toString() === 'function_exists';
            if (!$isGuard) {
                continue;
            }

            foreach (Ast::find(new FileNode($if->stmts), Function_::class) as $function) {
                $wrapped[$function->name->toString()] = true;
            }
        }

        $errors = [];
        foreach (Ast::find($node, Function_::class) as $function) {
            $name = $function->name->toString();
            if (isset($wrapped[$name])) {
                continue;
            }

            $errors[] = Errors::at($function, self::IDENTIFIER, sprintf('%s must be wrapped in function_exists().', $name));
        }

        return $errors;
    }
}
