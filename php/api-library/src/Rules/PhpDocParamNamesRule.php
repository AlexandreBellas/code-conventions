<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Ast;
use AlexandreBellas\CodeConventions\PhpStan\DocComments;
use AlexandreBellas\CodeConventions\PhpStan\Errors;
use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PHPStan\Analyser\Scope;
use PHPStan\Node\FileNode;
use PHPStan\Rules\Rule;

/**
 * @param names are exactly the parameter names.
 *
 * @implements Rule<FileNode>
 */
final class PhpDocParamNamesRule implements Rule
{
    public const IDENTIFIER = 'apiLibrary.phpDocParamNames';

    public function getNodeType(): string
    {
        return FileNode::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $errors = [];
        foreach ([...Ast::find($node, Function_::class), ...Ast::find($node, ClassMethod::class)] as $function) {
            $parameters = [];
            foreach ($function->params as $param) {
                if ($param->var instanceof Variable && is_string($param->var->name)) {
                    $parameters[] = $param->var->name;
                }
            }

            $documented = [];
            foreach (DocComments::tags($function->getDocComment()) as $tag) {
                if ($tag['kind'] !== 'param' || $tag['name'] === null) {
                    continue;
                }

                $documented[] = $tag['name'];
                if (!in_array($tag['name'], $parameters, true)) {
                    $errors[] = Errors::at($function, self::IDENTIFIER, sprintf('@param $%s does not match a parameter of %s.', $tag['name'], $function->name->toString()));
                }
            }

            foreach ($parameters as $parameter) {
                if (!in_array($parameter, $documented, true)) {
                    $errors[] = Errors::at($function, self::IDENTIFIER, sprintf('Parameter $%s of %s is missing an @param tag with that name.', $parameter, $function->name->toString()));
                }
            }
        }

        return $errors;
    }
}
