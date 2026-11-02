<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\TestSupport;

trait LoadsFixtures
{
    protected function fixture(string $relative): string
    {
        $file = (new \ReflectionClass($this))->getFileName();
        $path = dirname((string) $file) . '/Fixtures/' . $relative;
        if (!is_file($path)) {
            throw new \RuntimeException('Missing fixture ' . $path);
        }

        return $path;
    }
}
