<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Ast;
use AlexandreBellas\CodeConventions\PhpStan\DocComments;
use AlexandreBellas\CodeConventions\PhpStan\Errors;
use AlexandreBellas\CodeConventions\PhpStan\Paths;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Node\FileNode;
use PHPStan\Rules\Rule;

/**
 * The entity class, Bling, and each public entity method link to developer.bling.com.br.
 *
 * @implements Rule<FileNode>
 */
final class RequireEntitySeeRule implements Rule
{
    public const IDENTIFIER = 'apiLibrary.requireEntitySee';

    public function getNodeType(): string
    {
        return FileNode::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $file = $scope->getFile();
        $isEntity = Paths::isEntityModuleClass($file);
        $isBling = Paths::isBlingFacade($file);
        if (!$isEntity && !$isBling) {
            return [];
        }

        $errors = [];
        foreach (Ast::find($node, Class_::class) as $class) {
            if ($class->isAnonymous()) {
                continue;
            }

            if (!DocComments::hasBlingSee($class->getDocComment())) {
                $name = $class->name?->toString() ?? 'class';
                $errors[] = Errors::at($class, self::IDENTIFIER, sprintf('%s has no @see link to developer.bling.com.br.', $name));
            }

            if (!$isEntity) {
                continue;
            }

            foreach ($class->getMethods() as $method) {
                if (!$this->requiresSee($method)) {
                    continue;
                }

                if (DocComments::hasBlingSee($method->getDocComment())) {
                    continue;
                }

                $errors[] = Errors::at($method, self::IDENTIFIER, sprintf('%s has no @see link to developer.bling.com.br.', $method->name->toString()));
            }
        }

        return $errors;
    }

    private function requiresSee(ClassMethod $method): bool
    {
        return $method->isPublic() && $method->name->toString() !== '__construct';
    }
}
