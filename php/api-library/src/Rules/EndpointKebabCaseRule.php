<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Ast;
use AlexandreBellas\CodeConventions\PhpStan\Errors;
use AlexandreBellas\CodeConventions\PhpStan\Paths;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Identifier;
use PhpParser\Node\Scalar\InterpolatedString;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Node\FileNode;
use PHPStan\Rules\Rule;

/**
 * Endpoint paths are double-quoted and their static segments are kebab-case.
 *
 * @implements Rule<FileNode>
 */
final class EndpointKebabCaseRule implements Rule
{
    public const IDENTIFIER = 'apiLibrary.endpointKebabCase';

    public function getNodeType(): string
    {
        return FileNode::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (!Paths::isEntityModuleClass($scope->getFile())) {
            return [];
        }

        $errors = [];
        foreach (Ast::find($node, Arg::class) as $arg) {
            if (!$arg->name instanceof Identifier || $arg->name->toString() !== 'endpoint') {
                continue;
            }

            $value = $arg->value;
            if ((!$value instanceof String_ || $value->getAttribute('kind') !== String_::KIND_DOUBLE_QUOTED)
                && !$value instanceof InterpolatedString
            ) {
                $errors[] = Errors::at($arg, self::IDENTIFIER, 'endpoint must be a double-quoted kebab-case path.');
                continue;
            }

            $text = Ast::stringText($value);
            if ($text === null) {
                continue;
            }

            foreach (explode('/', $text) as $segment) {
                $static = preg_replace('/\$\{[^}]+\}|\$[A-Za-z_][A-Za-z0-9_]*/', '', $segment) ?? $segment;
                if ($static === '') {
                    continue;
                }

                if (preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $static) === 1) {
                    continue;
                }

                $errors[] = Errors::at($arg, self::IDENTIFIER, sprintf('Endpoint segment "%s" must be kebab-case.', $static));
            }
        }

        return $errors;
    }
}
