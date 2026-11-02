<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules\RequireForwardedParams;

use AlexandreBellas\CodeConventions\PhpStan\Ast;
use AlexandreBellas\CodeConventions\PhpStan\Config;
use AlexandreBellas\CodeConventions\PhpStan\Errors;
use AlexandreBellas\CodeConventions\PhpStan\Paths;
use AlexandreBellas\CodeConventions\PhpStan\Types;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * A filter parameter is forwarded as queryParams, and $body is forwarded as body.
 *
 * @implements Rule<ClassMethod>
 */
final class RequireForwardedParamsRule implements Rule
{
    public const IDENTIFIER = 'apiLibrary.requireForwardedParams';

    public function __construct(private readonly Config $config) {}

    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (!Paths::isEntityModuleClass($scope->getFile()) || !$node->isPublic() || $node->name->toString() === '__construct') {
            return [];
        }

        $query = null;
        $body = null;
        foreach ($node->params as $param) {
            if (!$param->var instanceof Variable || !is_string($param->var->name)) {
                continue;
            }

            $paramName = $param->var->name;
            if ($query === null && ($paramName === 'params' || $this->isParamsType($param->type))) {
                $query = $paramName;
            }

            if ($paramName === 'body') {
                $body = $paramName;
            }
        }

        if ($query === null && $body === null) {
            return [];
        }

        $named = $this->requestOptions($node);
        $requestClass = $this->config->short($this->config->requestOptionsClass);
        $errors = [];
        if ($query !== null && !$this->forwards($named, 'queryParams', $query)) {
            $errors[] = Errors::at($node, self::IDENTIFIER, sprintf('$%s must be forwarded as the queryParams argument of %s.', $query, $requestClass));
        }

        if ($body !== null && !$this->forwards($named, 'body', $body)) {
            $errors[] = Errors::at($node, self::IDENTIFIER, sprintf('$%s must be forwarded as the body argument of %s.', $body, $requestClass));
        }

        return $errors;
    }

    private function isParamsType(?Node $type): bool
    {
        if ($type instanceof Node\UnionType || $type instanceof Node\IntersectionType) {
            foreach ($type->types as $inner) {
                if ($this->isParamsType($inner)) {
                    return true;
                }
            }

            return false;
        }

        if ($type instanceof Node\NullableType) {
            return $this->isParamsType($type->type);
        }

        if ($type instanceof Node\Name || $type instanceof Node\Identifier) {
            $short = $type instanceof Node\Name ? $type->getLast() : $type->toString();

            return str_ends_with($short, 'Params');
        }

        return Types::contains($type, 'Params');
    }

    /**
     * @return array<string, \PhpParser\Node\Expr>|null
     */
    private function requestOptions(ClassMethod $method): ?array
    {
        if ($method->stmts === null) {
            return null;
        }

        $created = (new NodeFinder())->findInstanceOf($method->stmts, New_::class);
        foreach ($created as $new) {
            if (!Ast::isRequestOptions($new, $this->config->short($this->config->requestOptionsClass))) {
                continue;
            }

            return Ast::namedArguments($new);
        }

        return null;
    }

    /**
     * @param array<string, \PhpParser\Node\Expr>|null $named
     */
    private function forwards(?array $named, string $argument, string $variable): bool
    {
        if ($named === null || !isset($named[$argument])) {
            return false;
        }

        $value = $named[$argument];

        return $value instanceof Variable && $value->name === $variable;
    }
}
