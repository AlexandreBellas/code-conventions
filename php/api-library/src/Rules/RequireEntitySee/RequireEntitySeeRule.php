<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules\RequireEntitySee;

use AlexandreBellas\CodeConventions\PhpStan\Ast;
use AlexandreBellas\CodeConventions\PhpStan\Config;
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
 * The entity class, the facade, and each public entity method link to the configured docs host.
 *
 * @implements Rule<FileNode>
 */
final class RequireEntitySeeRule implements Rule
{
    public const IDENTIFIER = 'apiLibrary.requireEntitySee';

    public function __construct(private readonly Config $config) {}

    public function getNodeType(): string
    {
        return FileNode::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $file = $scope->getFile();
        $isEntity = Paths::isEntityModuleClass($file);
        $isFacade = Paths::isFacade($file, $this->config->short($this->config->facadeClass));
        if (!$isEntity && !$isFacade) {
            return [];
        }

        $host = $this->config->docsHost;
        $errors = [];
        foreach (Ast::find($node, Class_::class) as $class) {
            if ($class->isAnonymous()) {
                continue;
            }

            if (!DocComments::hasDocsSee($class->getDocComment(), $host)) {
                $name = $class->name?->toString() ?? 'class';
                $errors[] = Errors::at($class, self::IDENTIFIER, sprintf('%s has no @see link to %s.', $name, $host));
            }

            if (!$isEntity) {
                continue;
            }

            foreach ($class->getMethods() as $method) {
                if (!$this->requiresSee($method)) {
                    continue;
                }

                if (DocComments::hasDocsSee($method->getDocComment(), $host)) {
                    continue;
                }

                $errors[] = Errors::at($method, self::IDENTIFIER, sprintf('%s has no @see link to %s.', $method->name->toString(), $host));
            }
        }

        return $errors;
    }

    private function requiresSee(ClassMethod $method): bool
    {
        return $method->isPublic() && $method->name->toString() !== '__construct';
    }
}
