<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan;

/**
 * Values a consumer sets in phpstan.neon. Nothing here names a product.
 */
final class Config
{
    /**
     * @param list<string> $requestDtoClasses
     */
    public function __construct(
        public readonly string $rootNamespace,
        public readonly string $docsHost,
        public readonly string $facadeClass,
        public readonly string $repositoryInterface,
        public readonly string $apiException,
        public readonly string $internalException,
        public readonly string $entityBaseClass,
        public readonly string $queryParamsClass,
        public readonly string $responseRootClass,
        public readonly string $responseObjectClass,
        public readonly string $responseContract,
        public readonly string $requestOptionsClass,
        public readonly string $prepareDateMethod,
        public readonly string $toArrayFunction,
        public readonly string $enumDocblockPrefix,
        public readonly array $requestDtoClasses,
        public readonly string $testResponseTrait,
    ) {}

    public function short(string $class): string
    {
        $class = ltrim($class, '\\');
        $pos = strrpos($class, '\\');

        return $pos === false ? $class : substr($class, $pos + 1);
    }

    public function facadeFileName(): string
    {
        return $this->short($this->facadeClass) . '.php';
    }

    public function throwsTag(): string
    {
        return $this->short($this->apiException) . '|' . $this->short($this->internalException);
    }
}
