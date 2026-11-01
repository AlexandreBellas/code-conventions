<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Ast;
use AlexandreBellas\CodeConventions\PhpStan\DocComments;
use AlexandreBellas\CodeConventions\PhpStan\Errors;
use AlexandreBellas\CodeConventions\PhpStan\Paths;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * A public entity method that calls the repository documents @throws BlingApiException|BlingInternalException.
 *
 * @implements Rule<ClassMethod>
 */
final class RequireRepositoryThrowsRule implements Rule
{
    public const IDENTIFIER = 'apiLibrary.requireRepositoryThrows';

    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (!Paths::isEntityModuleClass($scope->getFile()) || !$node->isPublic() || $node->stmts === null) {
            return [];
        }

        $calls = (new NodeFinder())->find($node->stmts, static fn (Node $candidate): bool => Ast::repositoryVerb($candidate) !== null);
        if ($calls === []) {
            return [];
        }

        if (DocComments::hasRepositoryThrows($node->getDocComment())) {
            return [];
        }

        return [
            Errors::at($node, self::IDENTIFIER, sprintf('%s must document `@throws BlingApiException|BlingInternalException`.', $node->name->toString())),
        ];
    }
}
