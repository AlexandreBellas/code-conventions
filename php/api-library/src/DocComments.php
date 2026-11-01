<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan;

use PhpParser\Comment\Doc;

final class DocComments
{
    public static function text(?Doc $doc): string
    {
        return $doc?->getText() ?? '';
    }

    public static function hasSummary(?Doc $doc): bool
    {
        if ($doc === null) {
            return false;
        }

        foreach (self::lines($doc) as $line) {
            if ($line !== '' && !str_starts_with($line, '@')) {
                return true;
            }
        }

        return false;
    }

    public static function hasBlingSee(?Doc $doc): bool
    {
        $text = self::text($doc);

        return str_contains($text, '@see') && str_contains($text, 'developer.bling.com.br');
    }

    public static function hasRepositoryThrows(?Doc $doc): bool
    {
        return (bool) preg_match('/@throws\s+BlingApiException\|BlingInternalException\b/', self::text($doc));
    }

    public static function hasInheritDoc(?Doc $doc): bool
    {
        return (bool) preg_match('/@inheritDoc\b/i', self::text($doc));
    }

    public static function hasReturn(?Doc $doc): bool
    {
        return (bool) preg_match('/@return\b/', self::text($doc));
    }

    /**
     * @return list<array{kind: string, type: string|null, name: string|null}>
     */
    public static function tags(?Doc $doc): array
    {
        if ($doc === null) {
            return [];
        }

        $tags = [];
        foreach (preg_split('/\R/', $doc->getText()) ?: [] as $raw) {
            $line = trim($raw);
            if (preg_match('/@param\s+(.+?)\s+\$([A-Za-z_][A-Za-z0-9_]*)\b/', $line, $match) === 1) {
                $tags[] = ['kind' => 'param', 'type' => trim($match[1]), 'name' => $match[2]];
                continue;
            }

            if (preg_match('/@(return|throws|see|inheritDoc)\b/i', $line, $match) === 1) {
                $tags[] = ['kind' => strtolower($match[1]), 'type' => null, 'name' => null];
            }
        }

        return $tags;
    }

    /**
     * Description lines stay above every tag, and the tags that are present
     * stay in the order @param, @return, @throws, @see.
     */
    public static function methodOrderViolation(?Doc $doc): ?string
    {
        if ($doc === null) {
            return null;
        }

        $rank = ['param' => 1, 'return' => 2, 'throws' => 3, 'see' => 4];
        $seenTag = false;
        $last = 0;

        foreach (preg_split('/\R/', $doc->getText()) ?: [] as $raw) {
            $line = self::cleanLine($raw);
            if ($line === '' || $line === '/' || $line === '/**') {
                continue;
            }

            if (preg_match('/^@([A-Za-z]+)/', $line, $match) === 1) {
                $seenTag = true;
                $kind = strtolower($match[1]);
                if (!isset($rank[$kind])) {
                    continue;
                }

                if ($rank[$kind] < $last) {
                    return 'Public entity method docblocks list the description, then @param, @return, @throws, and @see, in that order.';
                }

                $last = $rank[$kind];
                continue;
            }

            if ($seenTag) {
                return 'Public entity method docblocks list the description, then @param, @return, @throws, and @see, in that order.';
            }
        }

        return null;
    }

    /**
     * @return list<string>
     */
    private static function lines(Doc $doc): array
    {
        $lines = [];
        foreach (preg_split('/\R/', $doc->getText()) ?: [] as $raw) {
            $lines[] = self::cleanLine($raw);
        }

        return $lines;
    }

    private static function cleanLine(string $raw): string
    {
        $line = trim($raw);
        $line = preg_replace('#^/\*\*#', '', $line) ?? $line;
        $line = preg_replace('#\*/$#', '', $line) ?? $line;
        $line = trim($line);
        $line = preg_replace('#^\*\s?#', '', $line) ?? $line;

        return trim($line);
    }
}
