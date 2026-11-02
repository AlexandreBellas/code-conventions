<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Ast;
use AlexandreBellas\CodeConventions\PhpStan\Config;
use AlexandreBellas\CodeConventions\PhpStan\Errors;
use AlexandreBellas\CodeConventions\PhpStan\Paths;
use AlexandreBellas\CodeConventions\PhpStan\Types;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Node\FileNode;
use PHPStan\Rules\Rule;

/**
 * A DateTimeInterface parameter of a *Params constructor is passed to the configured date method.
 *
 * @implements Rule<FileNode>
 */
final class ParamsPrepareDateRule implements Rule
{
    public const IDENTIFIER = 'apiLibrary.paramsPrepareDate';

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
                if ($method->name->toString() !== '__construct' || $method->stmts === null) {
                    continue;
                }

                $errors = array_merge($errors, $this->check($method));
            }
        }

        return $errors;
    }

    /**
     * @return list<\PHPStan\Rules\IdentifierRuleError>
     */
    private function check(ClassMethod $method): array
    {
        $prepared = [];
        foreach ((new NodeFinder())->findInstanceOf($method->stmts ?? [], MethodCall::class) as $call) {
            if (!$call->name instanceof Identifier || $call->name->toString() !== $this->config->prepareDateMethod) {
                continue;
            }

            $argument = $call->args[0] ?? null;
            if ($argument instanceof Arg && $argument->value instanceof Variable && is_string($argument->value->name)) {
                $prepared[$argument->value->name] = true;
            }
        }

        $errors = [];
        foreach ($method->params as $param) {
            if (!$param->var instanceof Variable || !is_string($param->var->name)) {
                continue;
            }

            if (!Types::contains($param->type, 'DateTimeInterface') || isset($prepared[$param->var->name])) {
                continue;
            }

            $errors[] = Errors::at($param, self::IDENTIFIER, sprintf('$%s must be passed through %s.', $param->var->name, $this->config->prepareDateMethod));
        }

        return $errors;
    }
}
