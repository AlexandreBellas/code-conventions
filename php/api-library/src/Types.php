<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan;

use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\IntersectionType;
use PhpParser\Node\Name;
use PhpParser\Node\NullableType;
use PhpParser\Node\UnionType;

final class Types
{
    public static function contains(?Node $type, string $shortName): bool
    {
        if ($type instanceof Identifier) {
            return $type->toString() === $shortName;
        }

        if ($type instanceof Name) {
            return $type->getLast() === $shortName;
        }

        if ($type instanceof NullableType) {
            return self::contains($type->type, $shortName);
        }

        if ($type instanceof UnionType || $type instanceof IntersectionType) {
            foreach ($type->types as $inner) {
                if (self::contains($inner, $shortName)) {
                    return true;
                }
            }
        }

        return false;
    }

    public static function print(?Node $type): ?string
    {
        if ($type === null) {
            return null;
        }

        if ($type instanceof Identifier) {
            return $type->toString();
        }

        if ($type instanceof Name) {
            return $type->toCodeString();
        }

        if ($type instanceof NullableType) {
            $inner = self::print($type->type);

            return $inner === null ? null : '?' . $inner;
        }

        if ($type instanceof UnionType) {
            return self::join($type->types, '|');
        }

        if ($type instanceof IntersectionType) {
            return self::join($type->types, '&');
        }

        return null;
    }

    public static function normalize(string $type): string
    {
        $type = preg_replace('/\s+/', '', $type) ?? $type;
        $parts = explode('|', $type);
        $parts = array_map(static function (string $part): string {
            $nullable = str_starts_with($part, '?');
            if ($nullable) {
                $part = substr($part, 1);
            }

            $part = ltrim($part, '\\');

            return ($nullable ? '?' : '') . $part;
        }, $parts);

        return implode('|', $parts);
    }

    /**
     * @param Node[] $types
     */
    private static function join(array $types, string $glue): ?string
    {
        $printed = [];
        foreach ($types as $type) {
            $item = self::print($type);
            if ($item === null) {
                return null;
            }

            $printed[] = $item;
        }

        return implode($glue, $printed);
    }
}
