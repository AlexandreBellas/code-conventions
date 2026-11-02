# code-conventions

Shared lint and format rules for AlexandreBellas projects.

## JavaScript

Install from git, then pull in what you need:

```ts
import reactjs from "code-conventions/eslint/reactjs"
import apiLibrary from "code-conventions/eslint/api-library"
```

`code-conventions/eslint/reactjs` is the React plugin. Its ESLint key is `reactjs`. `code-conventions/eslint/api-library` is the API client plugin, key `api-library`.

```js
import { defineConfig } from "code-conventions/prettier"
```

## PHP

The PHP side is an API client preset: PHPStan rules, and a PHP-CS-Fixer config.

```bash
composer config repositories.code-conventions vcs https://github.com/AlexandreBellas/code-conventions.git
composer require --dev alexandrebellas/code-conventions:dev-cursor/php-api-library-2c68
```

After this merges, require `dev-main`.

Allow `phpstan/extension-installer` and set the parameters in [php/api-library/README.md](php/api-library/README.md). The fixer config is one line, documented in [php/php-cs-fixer/README.md](php/php-cs-fixer/README.md).
