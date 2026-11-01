# code-conventions

Shared lint and format rules for AlexandreBellas projects.

## JavaScript

Install from git, then pull in what you need:

```ts
import reactjs from "code-conventions/eslint/reactjs"
import apiLibrary from "code-conventions/eslint/api-library"
```

`code-conventions/eslint/reactjs` is the React plugin. Its ESLint key is `reactjs`. `code-conventions/eslint/api-library` is the Bling entity plugin, key `api-library`.

```js
import { defineConfig } from "code-conventions/prettier"
```

## PHP

The PHP side is the `api-library` preset: PHPStan rules for the Bling ERP API client, and a PHP-CS-Fixer config for the formatting those rules do not rewrite. Each custom rule enforces one standard.

### Install

The package is public. Require it from this repository:

```bash
composer config repositories.code-conventions vcs https://github.com/AlexandreBellas/code-conventions.git
composer require --dev alexandrebellas/code-conventions:dev-main
```

`dev-main` is the branch Composer installs. Until this lands on `main`, use the branch name with the `dev-` prefix, for example `dev-cursor/php-api-library-2c68`.

That install brings in PHPStan and PHP-CS-Fixer. The preset targets PHP 8.2.

### Wire PHPStan

`alexandrebellas/code-conventions` is a `phpstan-extension`. With `phpstan/extension-installer` the rules register themselves:

```bash
composer require --dev phpstan/extension-installer
composer config allow-plugins.phpstan/extension-installer true
```

Without the installer, include the neon file:

```neon
includes:
    - vendor/alexandrebellas/code-conventions/php/api-library/extension.neon

parameters:
    level: 8
    phpVersion: 80200
    paths:
        - src
```

`phpstan analyse` then runs the rules below on `src/`.

### Wire PHP-CS-Fixer

`.php-cs-fixer.php` at the project root:

```php
<?php return require __DIR__ . '/vendor/alexandrebellas/code-conventions/php/php-cs-fixer/api-library.php';
```

Run it from the project root. The config formats `src/` when that directory exists.

```bash
vendor/bin/php-cs-fixer fix
```

### PHPStan rules

| Rule | Enforces | Cursor rule |
| --- | --- | --- |
| `apiLibrary.noStrictTypes` | No `declare(strict_types)`. | `code-style/php` |
| `apiLibrary.doubleQuotedStrings` | PHP strings use double quotes, including nowdoc. | `code-style/php`, `code-style/linting-and-formatting` |
| `apiLibrary.utf8` | Files are UTF-8. | `code-style/linting-and-formatting` |
| `apiLibrary.oneTypePerFile` | One class, enum, interface, or trait per file. | `code-style/php` |
| `apiLibrary.fileNameMatchesType` | The file stem is the type name. `FooTest.spec.php` declares `FooTest`. | `code-style/php`, `code-style/naming` |
| `apiLibrary.namespaceMirrorsPath` | The namespace mirrors the path under `src/`. `@shared` is `Shared`. | `code-style/php` |
| `apiLibrary.testNamespace` | Specs use `Tests\Unit\` plus that namespace, without `__tests__`. | `code-style/linting-and-formatting`, `testing/entity-unit-tests` |
| `apiLibrary.readonlyFinalSchema` | A class under `Schema/` is `readonly final class`. | `code-style/php`, `code-style/schemas-and-enums` |
| `apiLibrary.paramsExtendQueryParams` | A `*Params` schema class extends `QueryParams`. | `architecture/entity-modules`, `code-style/schemas-and-enums` |
| `apiLibrary.responseExtendsBase` | `*Response` extends `BaseResponseRootObject`. `*ResponseData*` extends `BaseResponseObject`. | `code-style/schemas-and-enums` |
| `apiLibrary.entityExtendsBase` | The module class is a concrete, non-readonly subclass of `BaseEntity`. | `architecture/entity-modules` |
| `apiLibrary.entityModuleLayout` | The module is `<Module>.php`, `Enum/<Name>.php`, `Schema/<Operation>/`, `Schema/@shared/`, and `__tests__/`. | `architecture/entity-modules`, `code-style/naming` |
| `apiLibrary.noCrossEntityImports` | A module does not import another module. `Entities\Shared` stays allowed. | `architecture/overview`, `architecture/extending-entities` |
| `apiLibrary.sharedDoesNotImportEntities` | `@shared` and `Helpers` do not import an entity module. | `architecture/shared-and-helpers` |
| `apiLibrary.noHttpInEntities` | Entity files do not import `GuzzleHttp` or call `curl_*`. | `architecture/extending-entities`, `architecture/entity-modules` |
| `apiLibrary.noAuthorizationInEntities` | Entity files do not set an `Authorization` header. | `architecture/overview`, `architecture/auth-layer` |
| `apiLibrary.noBracketQueryNames` | An array key does not already end with `[]`. | `architecture/repository-layer` |
| `apiLibrary.requireEntitySee` | The entity class, `Bling`, and each public entity method `@see` `developer.bling.com.br`. | `documentation/phpdoc`, `documentation/api-reference` |
| `apiLibrary.requireRepositoryThrows` | A public entity method that calls the repository documents `@throws BlingApiException\|BlingInternalException`. | `architecture/exceptions-and-errors`, `documentation/phpdoc` |
| `apiLibrary.phpDocParamNames` | `@param` names are exactly the parameter names. | `documentation/phpdoc` |
| `apiLibrary.phpDocParamTypes` | An `@param` type is the signature type. | `documentation/phpdoc`, `code-style/schemas-and-enums` |
| `apiLibrary.requireEntityReturnDoc` | Each public entity method documents `@return`. | `documentation/phpdoc` |
| `apiLibrary.entityMethodDocOrder` | That docblock is the description, then `@param`, `@return`, `@throws`, `@see`. | `documentation/phpdoc` |
| `apiLibrary.requireDocblockSummary` | The entity class, `Bling`, and schema classes have a one-line summary. | `documentation/phpdoc` |
| `apiLibrary.enumDocblock` | An enum docblock contains `Enumerador de`. | `code-style/schemas-and-enums`, `documentation/phpdoc` |
| `apiLibrary.backedEnum` | An enum under `Enum/` is backed by `string` or `int`. | `code-style/php`, `code-style/schemas-and-enums` |
| `apiLibrary.screamingSnakeEnumCase` | Enum cases are `SCREAMING_SNAKE`. | `code-style/naming` |
| `apiLibrary.paramsPrepareDate` | A `DateTimeInterface` argument of a `*Params` constructor is passed to `prepareStringOrDateParam`. | `code-style/schemas-and-enums`, `architecture/entity-modules` |
| `apiLibrary.paramsCallParent` | That constructor finishes with `parent::__construct(objectToArray($this))`. | `architecture/shared-and-helpers` |
| `apiLibrary.entityRepositoryCall` | A public entity method calls one repository verb with `new RequestOptions(...)` and returns `*Response::fromResponse(...)`. | `architecture/entity-modules` |
| `apiLibrary.requireForwardedParams` | A `*Params` or `$params` argument is forwarded as `queryParams`. `$body` is forwarded as `body`. | `architecture/entity-modules`, `architecture/extending-entities` |
| `apiLibrary.endpointKebabCase` | `endpoint` is a double-quoted path whose static segments are kebab-case. | `code-style/naming`, `architecture/entity-modules` |
| `apiLibrary.requireFacadeRegistration` | `src/Bling.php` imports the module, documents `@property`, and calls `getModule(Name::class)`. | `architecture/bling-facade`, `architecture/extending-entities` |
| `apiLibrary.requireEntitySpec` | The module has `__tests__/<Module>Test.spec.php`. | `testing/entity-unit-tests` |
| `apiLibrary.fixturePlacement` | JSON fixtures are not under `Schema/`. Extra PHP files are not under `__tests__/`. | `testing/fixtures`, `architecture/extending-entities` |
| `apiLibrary.testMethodName` | Entity spec methods are `testShould<Action>Successfully`. | `testing/entity-unit-tests` |
| `apiLibrary.entityTestExtendsTestCase` | An entity spec extends `PHPUnit\Framework\TestCase`. | `testing/entity-unit-tests` |
| `apiLibrary.entityTestUsesTrait` | An entity spec uses `TestResponseTrait`. | `testing/entity-unit-tests`, `testing/repository-test-doubles` |
| `apiLibrary.requireGetInstance` | An entity spec defines `private function getInstance`. | `testing/entity-unit-tests` |
| `apiLibrary.requireInheritDoc` | `fromResponse` and `fromRules` document `@inheritDoc` when they implement the base contract. The interface and the original declaration do not. | `documentation/phpdoc` |
| `apiLibrary.requireRepositoryInheritDoc` | A method that implements `IBlingRepository` documents `@inheritDoc`. | `documentation/phpdoc` |
| `apiLibrary.readonlyRequestDto` | `QueryParams`, `Body`, and `Headers` are non-final `readonly class`. | `architecture/shared-and-helpers`, `code-style/php` |
| `apiLibrary.functionExistsWrapper` | Each function in `src/Helpers/functions.php` is wrapped in `function_exists()`. | `architecture/shared-and-helpers` |
| `apiLibrary.entityMethodsArePublic` | Methods on the entity module class are public. | `architecture/entity-modules`, `code-style/php` |

These follow the same shape as `code-conventions/eslint/api-library`: path-scoped checks for entity modules, no per-file ignore list, and one identifier per standard. `noHttpInEntities`, `noCrossEntityImports`, `noAuthorizationInEntities`, `noBracketQueryNames`, `requireEntitySee`, `entityRepositoryCall`, `requireForwardedParams`, `requireFacadeRegistration`, and `requireEntitySpec` are the PHP forms of that ESLint preset.

### PHP-CS-Fixer rules

PHP-CS-Fixer does not ship a fixer that rewrites single quotes to double quotes. Double quotes stay on `apiLibrary.doubleQuotedStrings`.

| Fixer | Enforces | Cursor rule |
| --- | --- | --- |
| `indentation_type` | Indent with spaces, not tabs. The indent unit is 4 spaces. | `code-style/linting-and-formatting`, `code-style/php` |
| `statement_indentation` | Statement nesting uses that 4-space indent. | `code-style/linting-and-formatting`, `code-style/php` |
| `line_ending` | LF line endings. | `code-style/linting-and-formatting` |
| `single_blank_line_at_eof` | The file ends with a newline. | `code-style/linting-and-formatting` |
| `no_closing_tag` | No closing `?>`. | `code-style/php` |
| `blank_line_after_opening_tag` | `<?php` is followed by a blank line. | `code-style/php` |
| `single_import_per_statement` | One `use` per line. Group imports are split. | `code-style/linting-and-formatting`, `code-style/php` |
| `group_import: false` | Imports are not collapsed into groups. | `code-style/linting-and-formatting` |
| `no_unused_imports` | A `use` names a symbol the file references. | `code-style/linting-and-formatting`, `code-style/php` |
| `nullable_type_declaration_for_default_null_value` | A parameter defaulting to `null` uses nullable syntax (`?Type $name = null`). | `code-style/php` |
| `nullable_type_declaration` (`question_mark`) | A single nullable type is `?int`, not `int\|null`. Unions of more than two types stay unions. | `code-style/php` |
