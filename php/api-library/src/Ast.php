<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\InterpolatedString;
use PhpParser\Node\Scalar\String_;
use PhpParser\NodeFinder;
use PHPStan\Node\FileNode;

final class Ast
{
    public const REPOSITORY_VERBS = ['index', 'show', 'store', 'update', 'replace', 'destroy'];

    public const REQUEST_OPTION_NAMES = ['endpoint', 'queryParams', 'headers', 'body'];

    /**
     * @template T of Node
     * @param class-string<T> $class
     * @return list<T>
     */
    public static function find(FileNode $file, string $class): array
    {
        $found = (new NodeFinder())->findInstanceOf($file->getNodes(), $class);

        /** @var list<T> $found */
        return $found;
    }

    public static function repositoryVerb(Node $node): ?string
    {
        if (!$node instanceof MethodCall || !$node->name instanceof Identifier) {
            return null;
        }

        $verb = $node->name->toString();
        if (!in_array($verb, self::REPOSITORY_VERBS, true)) {
            return null;
        }

        $fetch = $node->var;
        if (!$fetch instanceof PropertyFetch || !$fetch->name instanceof Identifier) {
            return null;
        }

        if ($fetch->name->toString() !== 'repository') {
            return null;
        }

        if (!$fetch->var instanceof Variable || $fetch->var->name !== 'this') {
            return null;
        }

        return $verb;
    }

    public static function isRequestOptions(New_ $new): bool
    {
        return $new->class instanceof Name && $new->class->getLast() === 'RequestOptions';
    }

    /**
     * @return array<string, Expr>|null
     */
    public static function namedArguments(New_ $new): ?array
    {
        $named = [];
        foreach ($new->args as $arg) {
            if (!$arg instanceof Arg || $arg->name === null) {
                return null;
            }

            $named[$arg->name->toString()] = $arg->value;
        }

        return $named;
    }

    public static function isFromResponse(Expr $expr): bool
    {
        if (!$expr instanceof StaticCall || !$expr->name instanceof Identifier) {
            return false;
        }

        if ($expr->name->toString() !== 'fromResponse' || !$expr->class instanceof Name) {
            return false;
        }

        return str_ends_with($expr->class->getLast(), 'Response');
    }

    public static function isParentObjectToArray(Node $node): bool
    {
        if (!$node instanceof StaticCall || !$node->class instanceof Name || !$node->name instanceof Identifier) {
            return false;
        }

        if (strtolower($node->class->toString()) !== 'parent' || $node->name->toString() !== '__construct') {
            return false;
        }

        if (count($node->args) !== 1 || !$node->args[0] instanceof Arg) {
            return false;
        }

        $argument = $node->args[0]->value;
        if (!$argument instanceof FuncCall || !$argument->name instanceof Name) {
            return false;
        }

        if ($argument->name->toString() !== 'objectToArray' || count($argument->args) !== 1) {
            return false;
        }

        $inner = $argument->args[0];

        return $inner instanceof Arg
            && $inner->value instanceof Variable
            && $inner->value->name === 'this';
    }

    public static function stringText(Expr $expr): ?string
    {
        if ($expr instanceof String_) {
            return $expr->value;
        }

        if (!$expr instanceof InterpolatedString) {
            return null;
        }

        $text = '';
        foreach ($expr->parts as $part) {
            if ($part instanceof Node\InterpolatedStringPart) {
                $text .= $part->value;
                continue;
            }

            if ($part instanceof Variable && is_string($part->name)) {
                $text .= '$' . $part->name;
                continue;
            }

            $text .= '${expr}';
        }

        return $text;
    }
}
