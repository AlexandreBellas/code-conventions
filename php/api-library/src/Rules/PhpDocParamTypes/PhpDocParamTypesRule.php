<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules\PhpDocParamTypes;

use AlexandreBellas\CodeConventions\PhpStan\Ast;
use AlexandreBellas\CodeConventions\PhpStan\DocComments;
use AlexandreBellas\CodeConventions\PhpStan\Errors;
use AlexandreBellas\CodeConventions\PhpStan\Types;
use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PHPStan\Analyser\Scope;
use PHPStan\Node\FileNode;
use PHPStan\Rules\Rule;

/**
 * An @param type is the same type the signature uses.
 *
 * @implements Rule<FileNode>
 */
final class PhpDocParamTypesRule implements Rule
{
    public const IDENTIFIER = 'apiLibrary.phpDocParamTypes';

    public function getNodeType(): string
    {
        return FileNode::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $errors = [];
        foreach ([...Ast::find($node, Function_::class), ...Ast::find($node, ClassMethod::class)] as $function) {

            $signatures = [];
            foreach ($function->params as $param) {
                if (!$param->var instanceof Variable || !is_string($param->var->name)) {
                    continue;
                }

                $printed = Types::print($param->type);
                if ($printed === null) {
                    continue;
                }

                $signatures[$param->var->name] = Types::normalize($printed);
            }

            foreach (DocComments::tags($function->getDocComment()) as $tag) {
                if ($tag['kind'] !== 'param' || $tag['name'] === null || $tag['type'] === null) {
                    continue;
                }

                if (!isset($signatures[$tag['name']])) {
                    continue;
                }

                $documented = Types::normalize($tag['type']);
                if ($documented === $signatures[$tag['name']]) {
                    continue;
                }

                $errors[] = Errors::at(
                    $function,
                    self::IDENTIFIER,
                    sprintf('@param $%s of %s must use the signature type %s.', $tag['name'], $function->name->toString(), $signatures[$tag['name']]),
                );
            }
        }

        return $errors;
    }
}
