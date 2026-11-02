<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules\ParamsExtendQueryParams;

use AlexandreBellas\CodeConventions\PhpStan\Config;
use AlexandreBellas\CodeConventions\PhpStan\Errors;
use AlexandreBellas\CodeConventions\PhpStan\Paths;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * A `*Params` schema class extends the configured query class.
 *
 * @implements Rule<Class_>
 */
final class ParamsExtendQueryParamsRule implements Rule
{
    public const IDENTIFIER = 'apiLibrary.paramsExtendQueryParams';

    public function __construct(private readonly Config $config) {}

    public function getNodeType(): string
    {
        return Class_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $name = $node->name?->toString();
        if ($name === null || $node->isAnonymous() || !Paths::isSchema($scope->getFile()) || !str_ends_with($name, 'Params')) {
            return [];
        }

        $expected = $this->config->short($this->config->queryParamsClass);
        if ($node->extends !== null && $node->extends->getLast() === $expected) {
            return [];
        }

        return [
            Errors::at($node, self::IDENTIFIER, sprintf('%s must extend %s.', $name, $expected)),
        ];
    }
}
