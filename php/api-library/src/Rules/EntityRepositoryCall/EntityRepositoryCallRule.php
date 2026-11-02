<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules\EntityRepositoryCall;

use AlexandreBellas\CodeConventions\PhpStan\Ast;
use AlexandreBellas\CodeConventions\PhpStan\Config;
use AlexandreBellas\CodeConventions\PhpStan\Errors;
use AlexandreBellas\CodeConventions\PhpStan\Paths;
use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * A public entity method calls one repository verb with the configured request class and returns *Response::fromResponse().
 *
 * @implements Rule<ClassMethod>
 */
final class EntityRepositoryCallRule implements Rule
{
    public const IDENTIFIER = 'apiLibrary.entityRepositoryCall';

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

        $name = $node->name->toString();
        $errors = [];
        $calls = $node->stmts === null
            ? []
            : (new NodeFinder())->find($node->stmts, static fn (Node $candidate): bool => Ast::repositoryVerb($candidate) !== null);

        if (count($calls) !== 1) {
            $errors[] = Errors::at($node, self::IDENTIFIER, sprintf('Public entity method %s must call exactly one of $this->repository->index, show, store, update, replace, or destroy.', $name));
        } else {
            $call = $calls[0];
            $argument = $call->args[0] ?? null;
            $new = ($argument instanceof Node\Arg) ? $argument->value : null;
            $requestClass = $this->config->short($this->config->requestOptionsClass);
            $named = ($new instanceof New_ && Ast::isRequestOptions($new, $requestClass)) ? Ast::namedArguments($new) : null;
            $allowed = $named !== null && isset($named['endpoint']) && array_diff(array_keys($named), Ast::REQUEST_OPTION_NAMES) === [];
            if (count($call->args) !== 1 || !$allowed) {
                $errors[] = Errors::at($node, self::IDENTIFIER, sprintf('%s must pass a single new %s(...) using only the named arguments endpoint, queryParams, headers, and body.', $name, $requestClass));
            }
        }

        $returns = $node->stmts === null ? [] : (new NodeFinder())->findInstanceOf($node->stmts, Return_::class);
        $fromResponse = array_values(array_filter(
            $returns,
            static fn (Return_ $return): bool => $return->expr !== null && Ast::isFromResponse($return->expr),
        ));
        $returnType = $node->returnType;
        $typeOk = ($returnType instanceof Identifier && $returnType->toString() === 'null')
            || ($returnType instanceof Name && str_ends_with($returnType->getLast(), 'Response'));
        if (count($returns) !== 1 || count($fromResponse) !== 1 || !$typeOk) {
            $errors[] = Errors::at($node, self::IDENTIFIER, sprintf('%s must return *Response::fromResponse(...), and the return type must be that response class or null.', $name));
        }

        return $errors;
    }
}
