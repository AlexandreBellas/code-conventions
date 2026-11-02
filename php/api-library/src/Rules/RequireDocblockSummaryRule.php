<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Config;
use AlexandreBellas\CodeConventions\PhpStan\DocComments;
use AlexandreBellas\CodeConventions\PhpStan\Errors;
use AlexandreBellas\CodeConventions\PhpStan\Paths;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * Entity classes, the facade, and schema classes have a one-line docblock summary.
 *
 * @implements Rule<Class_>
 */
final class RequireDocblockSummaryRule implements Rule
{
    public const IDENTIFIER = 'apiLibrary.requireDocblockSummary';

    public function __construct(private readonly Config $config) {}

    public function getNodeType(): string
    {
        return Class_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if ($node->isAnonymous()) {
            return [];
        }

        $file = $scope->getFile();
        $applies = Paths::isEntityModuleClass($file)
            || Paths::isFacade($file, $this->config->short($this->config->facadeClass))
            || Paths::isSchema($file);
        if (!$applies || DocComments::hasSummary($node->getDocComment())) {
            return [];
        }

        $name = $node->name?->toString() ?? 'class';

        return [
            Errors::at($node, self::IDENTIFIER, sprintf('%s must have a one-line docblock summary.', $name)),
        ];
    }
}
