<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\TestSupport;

use AlexandreBellas\CodeConventions\PhpStan\Config;

final class NeutralConfig
{
    public static function make(): Config
    {
        return new Config(
            rootNamespace: 'Acme\\ApiClient',
            docsHost: 'docs.example.com',
            facadeClass: 'Client',
            repositoryInterface: 'ApiRepository',
            apiException: 'AcmeApiException',
            internalException: 'AcmeInternalException',
            entityBaseClass: 'Entity',
            queryParamsClass: 'Query',
            responseRootClass: 'ResponseRoot',
            responseObjectClass: 'ResponseData',
            responseContract: 'ResponseContract',
            requestOptionsClass: 'Request',
            prepareDateMethod: 'normalizeDate',
            toArrayFunction: 'toArray',
            enumDocblockPrefix: 'Set of',
            requestDtoClasses: ['Query', 'Payload', 'Headers'],
            testResponseTrait: 'FakeResponse',
        );
    }
}
