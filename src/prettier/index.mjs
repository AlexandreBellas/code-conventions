/**
 * Shared Prettier plugins for AlexandreBellas projects.
 *
 * Plugins are chained in registration order (each wraps the previous
 * TypeScript parser). Keep this order when selecting a subset, or use
 * `defineConfig()` / `plugins` for the full recommended set.
 *
 * @example Full recommended set
 * ```js
 * import { defineConfig } from "code-conventions/prettier"
 * export default defineConfig({ semi: false, tabWidth: 4 })
 * ```
 *
 * @example Pick individual plugins
 * ```js
 * export default {
 *   plugins: [
 *     "code-conventions/prettier/region-organizer",
 *     "code-conventions/prettier/eslint-directive-remover"
 *   ]
 * }
 * ```
 */

/** @typedef {import("prettier").Config} PrettierConfig */

/** Full recommended plugin chain (order matters). */
export const plugins = [
    "code-conventions/prettier/region-organizer",
    "code-conventions/prettier/eslint-directive-remover",
    "code-conventions/prettier/use-translation-merger",
    "code-conventions/prettier/useeffect-comment-lifter",
    "code-conventions/prettier/union-type-formatter"
]

/**
 * Merge project Prettier options with the recommended plugin chain.
 * Existing `plugins` from `config` are appended after the recommended ones.
 *
 * @param {PrettierConfig} [config]
 * @returns {PrettierConfig}
 */
export function defineConfig(config = {}) {
    return {
        ...config,
        plugins: [...plugins, ...(config.plugins ?? [])]
    }
}

export default { plugins }
