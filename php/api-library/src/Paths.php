<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan;

/**
 * Path checks for the api-library preset. Mirrors the entity layout in bling-erp-api-php.
 */
final class Paths
{
    public const NAMESPACE_ROOT = 'AleBatistella\\BlingErpApi';

    public static function normalize(string $filename): string
    {
        return str_replace('\\', '/', $filename);
    }

    public static function isTest(string $filename): bool
    {
        $path = self::normalize($filename);

        return str_contains($path, '/__tests__/') || str_ends_with($path, '.spec.php');
    }

    public static function isBlingFacade(string $filename): bool
    {
        return (bool) preg_match('#(?:^|/)src/Bling\.php$#', self::normalize($filename));
    }

    public static function isHelpersFunctions(string $filename): bool
    {
        return (bool) preg_match('#(?:^|/)src/Helpers/functions\.php$#', self::normalize($filename));
    }

    public static function isSharedOrHelpers(string $filename): bool
    {
        $path = self::normalize($filename);

        return (bool) preg_match('#(?:^|/)src/Entities/@shared(?:/|$)#', $path)
            || (bool) preg_match('#(?:^|/)src/Helpers(?:/|$)#', $path);
    }

    public static function isSchema(string $filename): bool
    {
        return str_contains(self::normalize($filename), '/Schema/');
    }

    public static function isEnumFile(string $filename): bool
    {
        return (bool) preg_match('#/Enum/[^/]+\.php$#', self::normalize($filename));
    }

    public static function isRequestDto(string $filename): bool
    {
        return str_contains(self::normalize($filename), '/DTO/Request/');
    }

    /**
     * @return array{module: string, rest: string}|null
     */
    public static function entityModule(string $filename): ?array
    {
        if (!preg_match('#(?:^|/)src/Entities/([^/]+)/(.+)$#', self::normalize($filename), $match)) {
            return null;
        }

        return [
            'module' => $match[1],
            'rest' => $match[2],
        ];
    }

    public static function isEntityModuleClass(string $filename): bool
    {
        $located = self::entityModule($filename);

        return $located !== null
            && $located['module'] !== '@shared'
            && $located['rest'] === $located['module'] . '.php';
    }

    public static function isEntitySource(string $filename): bool
    {
        $located = self::entityModule($filename);

        return $located !== null && !self::isTest($filename);
    }

    public static function srcRelative(string $filename): ?string
    {
        if (!preg_match('#(?:^|/)src/(.+)$#', self::normalize($filename), $match)) {
            return null;
        }

        return $match[1];
    }

    public static function expectedNamespace(string $filename): ?string
    {
        $relative = self::srcRelative($filename);
        if ($relative === null) {
            return null;
        }

        return self::namespaceFromRelativeDir(dirname($relative));
    }

    public static function expectedTestNamespace(string $filename): ?string
    {
        $relative = self::srcRelative($filename);
        if ($relative === null) {
            return null;
        }

        $dir = dirname($relative);
        $dir = preg_replace('#/__tests__$#', '', $dir) ?? $dir;
        $dir = str_replace('/__tests__/', '/', $dir);
        $production = self::namespaceFromRelativeDir($dir);

        return 'Tests\\Unit\\' . $production;
    }

    public static function typeStem(string $filename): string
    {
        $base = basename(self::normalize($filename));
        if (str_ends_with($base, '.spec.php')) {
            return substr($base, 0, -strlen('.spec.php'));
        }

        return basename($base, '.php');
    }

    public static function facadeFile(string $entityFilename): ?string
    {
        if (!preg_match('#^(.*?)/src/Entities/#', self::normalize($entityFilename), $match)) {
            return null;
        }

        return $match[1] . '/src/Bling.php';
    }

    public static function moduleDirectory(string $entityFilename): ?string
    {
        $located = self::entityModule($entityFilename);
        if ($located === null) {
            return null;
        }

        if (!preg_match('#^(.*?)/src/Entities/#', self::normalize($entityFilename), $match)) {
            return null;
        }

        return $match[1] . '/src/Entities/' . $located['module'];
    }

    private static function namespaceFromRelativeDir(string $dir): string
    {
        if ($dir === '.' || $dir === '') {
            return self::NAMESPACE_ROOT;
        }

        $dir = str_replace('@shared', 'Shared', $dir);

        return self::NAMESPACE_ROOT . '\\' . str_replace('/', '\\', $dir);
    }
}
