<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests;

trait LoadsFixtures
{
    protected function fixture(string $relative): string
    {
        $short = (new \ReflectionClass($this))->getShortName();
        $rule = preg_replace('/Test$/', '', $short) ?? $short;
        $path = __DIR__ . '/' . $rule . 'Fixtures/' . $relative;
        if (!is_file($path)) {
            throw new \RuntimeException('Missing fixture ' . $path);
        }

        return $path;
    }
}
