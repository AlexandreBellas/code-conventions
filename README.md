# code-conventions

Shared ESLint rules and Prettier plugins used across AlexandreBellas projects.

Install from git, then pull in what you need:

```ts
import reactjs from "code-conventions/eslint/reactjs"
import apiLibrary from "code-conventions/eslint/api-library"
```

`code-conventions/eslint/reactjs` is the React plugin. Its ESLint key is `reactjs`. `code-conventions/eslint/api-library` is the Bling entity plugin, key `api-library`.

```js
import { defineConfig } from "code-conventions/prettier"
```
