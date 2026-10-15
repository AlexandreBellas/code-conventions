import { RuleTester } from "@typescript-eslint/rule-tester"
import { afterAll, describe, it, vi } from "vitest"

vi.mock("node:fs", () => {
    const sourceFiles = new Set([
        "src/components/Foo/index.tsx",
        "src/components/Mixed/index.tsx",
        "src/components/Buttons/PreviewButton/index.tsx",
        "src/hooks/useTest.ts",
        "src/legacy/thing.js",
        "src/utils/testing/helper.ts",
        ".eslint/rules/enforce-test-colocation.ts"
    ])
    const existsSyncFn = vi.fn((p: string) => sourceFiles.has(p))
    const mock = { existsSync: existsSyncFn }
    return { default: mock, ...mock }
})

import rule from "./enforce-test-colocation"

RuleTester.afterAll = afterAll
RuleTester.describe = describe
RuleTester.it = it

const ruleTester = new RuleTester({
    languageOptions: {
        parserOptions: {
            ecmaFeatures: { jsx: true }
        }
    }
})

const CODE = "export const value = 1"

// #region — bannedTestFolder: files inside __tests__ / nested tests, test, spec folders

ruleTester.run("enforce-test-colocation (bannedTestFolder)", rule, {
    valid: [
        // #region — Folders whose names only look like test folders

        {
            name: "folder named testing is not a test folder",
            code: CODE,
            filename: "src/utils/testing/helper.ts"
        },
        {
            name: "folder named test-utils is not a test folder",
            code: CODE,
            filename: "src/utils/test-utils/helper.ts"
        },
        {
            name: "folder prefixed with __tests__ is not a test folder",
            code: CODE,
            filename: "src/utils/__tests__helpers/helper.ts"
        },
        {
            name: "folder named specs is not a test folder",
            code: CODE,
            filename: "src/utils/specs/helper.ts"
        },
        {
            name: "file named test.ts is not a test folder",
            code: CODE,
            filename: "src/utils/test.ts"
        },

        // #endregion

        // #region — Top-level test folders stay legal

        {
            name: "test folder directly inside src",
            code: CODE,
            filename: "src/test/setupDomMocks.ts"
        },
        {
            name: "tests folder directly inside src",
            code: CODE,
            filename: "src/tests/examples/rows.ts"
        },
        {
            name: "spec folder directly inside src",
            code: CODE,
            filename: "src/spec/helpers.ts"
        },
        {
            name: "repository-root test folder",
            code: CODE,
            filename: "test/setupDomMocks.ts"
        },
        {
            name: "repository-root tests folder",
            code: CODE,
            filename: "tests/fixtures/data.ts"
        },
        {
            name: "test file in a src-level tests folder needs no sibling module",
            code: CODE,
            filename: "src/tests/integration/checkout.test.ts"
        },
        {
            name: "test file in a repository-root tests folder needs no sibling module",
            code: CODE,
            filename: "tests/integration/checkout.test.ts"
        }

        // #endregion
    ],
    invalid: [
        // #region — __tests__ is banned at every depth

        {
            name: "__tests__ folder beside a component",
            code: CODE,
            filename: "src/components/Foo/__tests__/index.test.tsx",
            errors: [{ messageId: "bannedTestFolder", data: { name: "index.test.tsx", folder: "__tests__" } }]
        },
        {
            name: "__tests__ folder at the project root",
            code: CODE,
            filename: "__tests__/smoke.test.ts",
            errors: [{ messageId: "bannedTestFolder", data: { name: "smoke.test.ts", folder: "__tests__" } }]
        },
        {
            name: "__tests__ folder several levels above the file",
            code: CODE,
            filename: "src/components/Foo/__tests__/fixtures/rows.ts",
            errors: [{ messageId: "bannedTestFolder", data: { name: "rows.ts", folder: "__tests__" } }]
        },
        {
            name: "__tests__ folder nested inside a repository-root tests folder",
            code: CODE,
            filename: "tests/__tests__/smoke.test.ts",
            errors: [{ messageId: "bannedTestFolder", data: { name: "smoke.test.ts", folder: "__tests__" } }]
        },
        {
            name: "__tests__ folder nested inside a src-level tests folder",
            code: CODE,
            filename: "src/tests/__tests__/smoke.test.ts",
            errors: [{ messageId: "bannedTestFolder", data: { name: "smoke.test.ts", folder: "__tests__" } }]
        },
        {
            name: "__tests__ folder reached through a backslash path",
            code: CODE,
            filename: "src\\components\\Foo\\__tests__\\index.test.tsx",
            errors: [{ messageId: "bannedTestFolder", data: { name: "index.test.tsx", folder: "__tests__" } }]
        },

        // #endregion

        // #region — tests, test and spec are banned below the top level

        {
            name: "tests folder one level deeper than src",
            code: CODE,
            filename: "src/utils/tests/attach-scroll-metrics.ts",
            errors: [{ messageId: "bannedTestFolder", data: { name: "attach-scroll-metrics.ts", folder: "tests" } }]
        },
        {
            name: "test folder one level deeper than src",
            code: CODE,
            filename: "src/utils/test/helpers.ts",
            errors: [{ messageId: "bannedTestFolder", data: { name: "helpers.ts", folder: "test" } }]
        },
        {
            name: "spec folder one level deeper than src",
            code: CODE,
            filename: "src/utils/spec/helpers.ts",
            errors: [{ messageId: "bannedTestFolder", data: { name: "helpers.ts", folder: "spec" } }]
        },
        {
            name: "tests folder deep inside a component tree",
            code: CODE,
            filename: "src/components/Foo/tests/helpers.ts",
            errors: [{ messageId: "bannedTestFolder", data: { name: "helpers.ts", folder: "tests" } }]
        },
        {
            name: "tests folder nested inside a legal src-level tests folder",
            code: CODE,
            filename: "src/tests/utils/tests/helpers.ts",
            errors: [{ messageId: "bannedTestFolder", data: { name: "helpers.ts", folder: "tests" } }]
        },
        {
            name: "test folder one level below a non-src top-level folder",
            code: CODE,
            filename: ".storybook/test/setup.ts",
            errors: [{ messageId: "bannedTestFolder", data: { name: "setup.ts", folder: "test" } }]
        },

        // #endregion

        // #region — The folder ban wins over the colocation check (one report per file)

        {
            name: "orphan test file inside a __tests__ folder reports the folder only",
            code: CODE,
            filename: "src/components/Foo/__tests__/nothing.test.ts",
            errors: [{ messageId: "bannedTestFolder", data: { name: "nothing.test.ts", folder: "__tests__" } }]
        }

        // #endregion
    ]
})

// #endregion

// #region — orphanTestFile: test files with no module of the same name beside them

ruleTester.run("enforce-test-colocation (orphanTestFile)", rule, {
    valid: [
        // #region — Test files sitting beside their module

        {
            name: "index.test.tsx beside index.tsx",
            code: CODE,
            filename: "src/components/Foo/index.test.tsx"
        },
        {
            name: "useTest.test.ts beside useTest.ts",
            code: CODE,
            filename: "src/hooks/useTest.test.ts"
        },
        {
            name: "index.test.ts beside index.tsx",
            code: CODE,
            filename: "src/components/Mixed/index.test.ts"
        },
        {
            name: "test file beside a plain js module",
            code: CODE,
            filename: "src/legacy/thing.test.js"
        },
        {
            name: "rule test file beside its rule module",
            code: CODE,
            filename: ".eslint/rules/enforce-test-colocation.test.ts"
        },

        // #endregion

        // #region — Files that are not test files

        {
            name: "module without any test file",
            code: CODE,
            filename: "src/components/Orphaned/index.tsx"
        },
        {
            name: "storybook file needs no module of its own name",
            code: CODE,
            filename: "src/components/Orphaned/index.stories.tsx"
        },
        {
            name: "test helper file whose name only contains .test.",
            code: CODE,
            filename: "src/utils/useStreamingMessages.test.utils.ts"
        },
        {
            name: "file named exactly test.ts is not a test file",
            code: CODE,
            filename: "src/utils/nothing/test.ts"
        }

        // #endregion
    ],
    invalid: [
        // #region — Test named after the folder instead of the module

        {
            name: "test named after its folder while the module is index.tsx",
            code: CODE,
            filename: "src/components/Buttons/PreviewButton/PreviewButton.test.tsx",
            errors: [{ messageId: "orphanTestFile", data: { name: "PreviewButton.test.tsx", base: "PreviewButton" } }]
        },

        // #endregion

        // #region — Cross-cutting tests with no module under test

        {
            name: "test file with no module of the same name",
            code: CODE,
            filename: "src/utils/string.test.ts",
            errors: [{ messageId: "orphanTestFile", data: { name: "string.test.ts", base: "string" } }]
        },
        {
            name: "tsx test file with no module of the same name",
            code: CODE,
            filename: "src/components/Foo/rendering.test.tsx",
            errors: [{ messageId: "orphanTestFile", data: { name: "rendering.test.tsx", base: "rendering" } }]
        },
        {
            name: "js test file with no module of the same name",
            code: CODE,
            filename: "src/pages/Conversation/ConversationErrors.test.js",
            errors: [{ messageId: "orphanTestFile", data: { name: "ConversationErrors.test.js", base: "ConversationErrors" } }]
        },
        {
            name: "spec file with no module of the same name",
            code: CODE,
            filename: "src/utils/string.spec.ts",
            errors: [{ messageId: "orphanTestFile", data: { name: "string.spec.ts", base: "string" } }]
        },
        {
            name: "test file whose module lives in another folder",
            code: CODE,
            filename: "src/components/Elsewhere/index.test.tsx",
            errors: [{ messageId: "orphanTestFile", data: { name: "index.test.tsx", base: "index" } }]
        }

        // #endregion
    ]
})

// #endregion
