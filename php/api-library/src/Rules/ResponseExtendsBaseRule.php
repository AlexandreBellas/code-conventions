<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Errors;
use AlexandreBellas\CodeConventions\PhpStan\Paths;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * `*Response` extends `BaseResponseRootObject`. `*ResponseData*` extends `BaseResponseObject`.
 *
 * @implements Rule<Class_>
 */
final class ResponseExtendsBaseRule implements Rule
{
    public const IDENTIFIER = 'apiLibrary.responseExtendsBase';

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
            $expected = 'BaseResponseObject';
        } elseif (str_ends_with($name, 'Response')) {
            $expected = 'BaseResponseRootObject';
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
