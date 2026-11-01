<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests;

trait WritesFixtures
{
    protected function fixture(string $relative, string $contents): string
    {
        $path = sys_get_temp_dir() . '/code-conventions-api-library/' . $relative;
        $directory = dirname($path);
        if (!is_dir($directory) && !mkdir($directory, 0777, true) && !is_dir($directory)) {
            throw new \RuntimeException('Cannot create ' . $directory);
        }

        file_put_contents($path, $contents);

        return $path;
    }
}
