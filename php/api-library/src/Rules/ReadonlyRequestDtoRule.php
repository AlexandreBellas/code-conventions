<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Config;
use AlexandreBellas\CodeConventions\PhpStan\Errors;
use AlexandreBellas\CodeConventions\PhpStan\Paths;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * The configured request DTO classes are `readonly class` and stay open for extension.
 *
 * @implements Rule<Class_>
 */
final class ReadonlyRequestDtoRule implements Rule
{
    public const IDENTIFIER = 'apiLibrary.readonlyRequestDto';

    public function __construct(private readonly Config $config) {}

    public function getNodeType(): string
    {
        return Class_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $name = $node->name?->toString();
        if ($name === null || $node->isAnonymous() || !Paths::isRequestDto($scope->getFile())) {
            return [];
        }

        $allowed = array_map($this->config->short(...), $this->config->requestDtoClasses);
        if (!in_array($name, $allowed, true)) {
            return [];
        }

        if ($node->isReadonly() && !$node->isFinal()) {
            return [];
        }

        return [
            Errors::at($node, self::IDENTIFIER, sprintf('%s must be a non-final `readonly class`.', $name)),
        ];
    }
}
