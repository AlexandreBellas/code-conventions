<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules\ResponseExtendsBase;

use AlexandreBellas\CodeConventions\PhpStan\Config;
use AlexandreBellas\CodeConventions\PhpStan\Errors;
use AlexandreBellas\CodeConventions\PhpStan\Paths;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * `*Response` extends the configured root class. `*ResponseData*` extends the configured object class.
 *
 * @implements Rule<Class_>
 */
final class ResponseExtendsBaseRule implements Rule
{
    public const IDENTIFIER = 'apiLibrary.responseExtendsBase';

    public function __construct(private readonly Config $config) {}

    public function getNodeType(): string
    {
        return Class_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $name = $node->name?->toString();
        if ($name === null || $node->isAnonymous() || !Paths::isSchema($scope->getFile())) {
            return [];
        }

        $expected = null;
        if (str_contains($name, 'ResponseData')) {
            $expected = $this->config->short($this->config->responseObjectClass);
        } elseif (str_ends_with($name, 'Response')) {
            $expected = $this->config->short($this->config->responseRootClass);
        }

        if ($expected === null) {
            return [];
        }

        if ($node->extends !== null && $node->extends->getLast() === $expected) {
            return [];
        }

        return [
            Errors::at($node, self::IDENTIFIER, sprintf('%s must extend %s.', $name, $expected)),
        ];
    }
}
