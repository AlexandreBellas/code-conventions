# PHPStan api-library

Rules for a PHP API client: one class per resource under `src/Entities`, schema classes, a facade, and a repository. Each rule checks one standard. There is no per-file ignore list.

The extension has no default parameter values. Set every field below or PHPStan will not boot.

## Parameters

| Parameter | Type | Purpose |
| --- | --- | --- |
| `apiLibrary.rootNamespace` | string | Root namespace. `@shared` on disk maps to the `Shared` segment. |
| `apiLibrary.docsHost` | string | Host an entity, facade, and public entity method `@see` must include. |
| `apiLibrary.facadeClass` | string | Facade class. The file is `src/<Class>.php`. |
| `apiLibrary.repositoryInterface` | string | Repository interface. Implementing methods document `@inheritDoc`. |
| `apiLibrary.apiException` | string | First class in the repository `@throws` tag. |
| `apiLibrary.internalException` | string | Second class in that tag, joined with `\|`. |
| `apiLibrary.entityBaseClass` | string | Concrete, non-readonly base of each module class. |
| `apiLibrary.queryParamsClass` | string | Base class of a `*Params` schema. |
| `apiLibrary.responseRootClass` | string | Base class of a `*Response` schema. |
| `apiLibrary.responseObjectClass` | string | Base class of a `*ResponseData*` schema. |
| `apiLibrary.responseContract` | string | Interface `fromResponse` implements. |
| `apiLibrary.requestOptionsClass` | string | Class passed to a repository verb. |
| `apiLibrary.prepareDateMethod` | string | Method a `DateTimeInterface` constructor argument must pass through. |
| `apiLibrary.toArrayFunction` | string | Function a `*Params` constructor passes to `parent::__construct`. |
| `apiLibrary.enumDocblockPrefix` | string | Text an enum docblock must contain. |
| `apiLibrary.requestDtoClasses` | list of strings | Request DTO classes that stay non-final `readonly class`. |
| `apiLibrary.testResponseTrait` | string | Trait an entity spec must use. |

Class names may be short (`Entity`) or fully qualified. The check uses the part after the last `\`.

## Example

```neon
includes:
    - vendor/alexandrebellas/code-conventions/php/api-library/extension.neon

parameters:
    level: 8
    phpVersion: 80200
    paths:
        - src
    apiLibrary:
        rootNamespace: Acme\ApiClient
        docsHost: docs.example.com
        facadeClass: Client
        repositoryInterface: ApiRepository
        apiException: AcmeApiException
        internalException: AcmeInternalException
        entityBaseClass: Entity
        queryParamsClass: Query
        responseRootClass: ResponseRoot
        responseObjectClass: ResponseData
        responseContract: ResponseContract
        requestOptionsClass: Request
        prepareDateMethod: normalizeDate
        toArrayFunction: toArray
        enumDocblockPrefix: "Set of"
        requestDtoClasses:
            - Query
            - Payload
            - Headers
        testResponseTrait: FakeResponse
```

With `phpstan/extension-installer` you can omit the `includes` entry. The parameters stay.

## Rules

| Rule | Enforces |
| --- | --- |
| `apiLibrary.noStrictTypes` | No `declare(strict_types)`. |
| `apiLibrary.doubleQuotedStrings` | PHP strings use double quotes, including nowdoc. |
| `apiLibrary.utf8` | Files are UTF-8. |
| `apiLibrary.oneTypePerFile` | One class, enum, interface, or trait per file. |
| `apiLibrary.fileNameMatchesType` | The file stem is the type name. `OrdersTest.spec.php` declares `OrdersTest`. |
| `apiLibrary.namespaceMirrorsPath` | The namespace mirrors the path under `src/`. `@shared` is `Shared`. |
| `apiLibrary.testNamespace` | Specs use `Tests\Unit\` plus that namespace, without `__tests__`. |
| `apiLibrary.readonlyFinalSchema` | A class under `Schema/` is `readonly final class`. |
| `apiLibrary.paramsExtendQueryParams` | A `*Params` schema class extends `queryParamsClass`. |
| `apiLibrary.responseExtendsBase` | `*Response` extends `responseRootClass`. `*ResponseData*` extends `responseObjectClass`. |
| `apiLibrary.entityExtendsBase` | The module class is a concrete, non-readonly subclass of `entityBaseClass`. |
| `apiLibrary.entityModuleLayout` | The module is `<Module>.php`, `Enum/<Name>.php`, `Schema/<Operation>/`, `Schema/@shared/`, and `__tests__/`. |
| `apiLibrary.noCrossEntityImports` | A module does not import another module. `Entities\Shared` stays allowed. |
| `apiLibrary.sharedDoesNotImportEntities` | `@shared` and `Helpers` do not import an entity module. |
| `apiLibrary.noHttpInEntities` | Entity files do not import `GuzzleHttp` or call `curl_*`. |
| `apiLibrary.noAuthorizationInEntities` | Entity files do not set an `Authorization` header. |
| `apiLibrary.noBracketQueryNames` | An array key does not already end with `[]`. |
| `apiLibrary.requireEntitySee` | The entity class, the facade, and each public entity method `@see` `docsHost`. |
| `apiLibrary.requireRepositoryThrows` | A public entity method that calls the repository documents `@throws apiException\|internalException`. |
| `apiLibrary.phpDocParamNames` | `@param` names are exactly the parameter names. |
| `apiLibrary.phpDocParamTypes` | An `@param` type is the signature type. |
| `apiLibrary.requireEntityReturnDoc` | Each public entity method documents `@return`. |
| `apiLibrary.entityMethodDocOrder` | That docblock is the description, then `@param`, `@return`, `@throws`, `@see`. |
| `apiLibrary.requireDocblockSummary` | The entity class, the facade, and schema classes have a one-line summary. |
| `apiLibrary.enumDocblock` | An enum docblock contains `enumDocblockPrefix`. |
| `apiLibrary.backedEnum` | An enum under `Enum/` is backed by `string` or `int`. |
| `apiLibrary.screamingSnakeEnumCase` | Enum cases are `SCREAMING_SNAKE`. |
| `apiLibrary.paramsPrepareDate` | A `DateTimeInterface` argument of a `*Params` constructor is passed to `prepareDateMethod`. |
| `apiLibrary.paramsCallParent` | That constructor finishes with `parent::__construct(<toArrayFunction>($this))`. |
| `apiLibrary.entityRepositoryCall` | A public entity method calls one repository verb with `new <requestOptionsClass>(...)` and returns `*Response::fromResponse(...)`. |
| `apiLibrary.requireForwardedParams` | A `*Params` or `$params` argument is forwarded as `queryParams`. `$body` is forwarded as `body`. |
| `apiLibrary.endpointKebabCase` | `endpoint` is a double-quoted path whose static segments are kebab-case. |
| `apiLibrary.requireFacadeRegistration` | The facade file imports the module, documents `@property`, and calls `getModule(Name::class)`. |
| `apiLibrary.requireEntitySpec` | The module has `__tests__/<Module>Test.spec.php`. |
| `apiLibrary.fixturePlacement` | JSON fixtures are not under `Schema/`. Extra PHP files are not under `__tests__/`. |
| `apiLibrary.testMethodName` | Entity spec methods are `testShould<Action>Successfully`. |
| `apiLibrary.entityTestExtendsTestCase` | An entity spec extends `PHPUnit\Framework\TestCase`. |
| `apiLibrary.entityTestUsesTrait` | An entity spec uses `testResponseTrait`. |
| `apiLibrary.requireGetInstance` | An entity spec defines `private function getInstance`. |
| `apiLibrary.requireInheritDoc` | `fromResponse` and `fromRules` document `@inheritDoc` when they implement the base contract. |
| `apiLibrary.requireRepositoryInheritDoc` | A method that implements `repositoryInterface` documents `@inheritDoc`. |
| `apiLibrary.readonlyRequestDto` | Each `requestDtoClasses` entry is a non-final `readonly class`. |
| `apiLibrary.functionExistsWrapper` | Each function in `src/Helpers/functions.php` is wrapped in `function_exists()`. |
| `apiLibrary.entityMethodsArePublic` | Methods on the entity module class are public. |

These follow the same shape as `code-conventions/eslint/api-library`: path checks for entity modules, and one identifier per standard.

PHP-CS-Fixer does not ship a fixer that rewrites single quotes to double quotes. Double quotes stay on `apiLibrary.doubleQuotedStrings`.
