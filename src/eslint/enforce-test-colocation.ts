import type { TSESLint } from "@typescript-eslint/utils"
import { existsSync } from "node:fs"
import { basename, dirname, join } from "node:path"

type IMessageIds = "bannedTestFolder" | "orphanTestFile"

const FOLDERS_BANNED_ANYWHERE = ["__tests__"]
const TEST_FOLDERS = ["tests", "test", "spec"]
const SOURCE_ROOT = "src"
const SOURCE_EXTENSIONS = [".ts", ".tsx", ".js", ".jsx"]
const TEST_FILE_RE = /^(.+)\.(?:test|spec)\.[jt]sx?$/
const FILE_START = { line: 1, column: 0 }

function normalizePath(filename: string): string {
    return filename.replace(/\\/g, "/")
}

function toProjectRelativePath(filename: string, cwd: string): string {
    const path = normalizePath(filename)
    const root = normalizePath(cwd).replace(/\/+$/, "")

    if (root.length > 0 && path.startsWith(root + "/")) return path.slice(root.length + 1)

    return path.replace(/^\/+/, "")
}

/**
 * A `tests`, `test` or `spec` folder is legal only at the top of the source tree — directly inside
 * `src`, or at the repository root. Anywhere deeper it detaches tests from the code they cover.
 */
function isTopLevelTestFolder(directories: string[], index: number): boolean {
    if (!TEST_FOLDERS.includes(directories[index])) return false
    if (index === 0) return true

    return index === 1 && directories[0] === SOURCE_ROOT
}

/**
 * Returns the banned folder segment this file sits in, or `null` when the path is clean.
 *
 * `__tests__` is banned at every depth, including inside an otherwise legal test folder.
 */
function findBannedFolder(relativePath: string): string | null {
    const directories = relativePath.split("/").slice(0, -1)

    for (let index = 0; index < directories.length; index++) {
        const directory = directories[index]

        if (FOLDERS_BANNED_ANYWHERE.includes(directory)) return directory
        if (TEST_FOLDERS.includes(directory) && !isTopLevelTestFolder(directories, index)) return directory
    }

    return null
}

/**
 * A top-level test folder is a legal home for cross-cutting tests, which by definition have no
 * module to sit next to — so colocation cannot be required there.
 */
function isInTopLevelTestFolder(relativePath: string): boolean {
    const directories = relativePath.split("/").slice(0, -1)

    for (let index = 0; index < directories.length; index++)
        if (isTopLevelTestFolder(directories, index)) return true

    return false
}

function hasModuleUnderTest(filename: string, base: string): boolean {
    const directory = dirname(filename)

    for (const extension of SOURCE_EXTENSIONS) if (existsSync(join(directory, base + extension))) return true

    return false
}

/**
 * A test that does not sit next to the module it covers, named after it, is invisible from that
 * module — so it is never opened when the module changes and silently rots when it is renamed
 * or deleted.
 *
 * Every test belongs in the folder of its subject as `<module>.test.ts(x)`: `index.tsx` is tested
 * by `index.test.tsx`, `useTest.ts` by `useTest.test.ts`. Cross-cutting tests with no single
 * subject belong in a top-level test folder such as `src/tests`.
 */
const rule: TSESLint.RuleModule<IMessageIds> = {
    meta: {
        type: "problem",
        docs: {
            description:
                "ban `__tests__` folders anywhere and `tests`/`test`/`spec` folders below the top of `src`, " +
                "and require every test file to sit next to the module it covers as `<module>.test.ts(x)`"
        },
        messages: {
            bannedTestFolder:
                '"{{ name }}" sits inside a "{{ folder }}" folder, which is not allowed here. ' +
                'Put each test beside the module it covers as "<module>.test.ts(x)" ' +
                '("index.tsx" is tested by "index.test.tsx"), and keep shared test helpers in ' +
                '"src/tests" or a folder not named after tests (for example "src/utils/testing").',
            orphanTestFile:
                '"{{ name }}" has no module under test — this folder holds no "{{ base }}.ts", ' +
                '"{{ base }}.tsx", "{{ base }}.js" or "{{ base }}.jsx". Name the test after the file it ' +
                'covers ("index.tsx" is tested by "index.test.tsx"), or move it beside that file.'
        },
        schema: []
    },
    defaultOptions: [],
    create(context) {
        const relativePath = toProjectRelativePath(context.filename, context.cwd)
        const name = basename(normalizePath(context.filename))
        const bannedFolder = findBannedFolder(relativePath)
        const testFileMatch = TEST_FILE_RE.exec(name)

        return {
            Program() {
                if (bannedFolder !== null) {
                    context.report({
                        loc: FILE_START,
                        messageId: "bannedTestFolder",
                        data: { name, folder: bannedFolder }
                    })
                    return
                }

                if (testFileMatch === null) return
                if (isInTopLevelTestFolder(relativePath)) return

                const base = testFileMatch[1]
                if (hasModuleUnderTest(context.filename, base)) return

                context.report({
                    loc: FILE_START,
                    messageId: "orphanTestFile",
                    data: { name, base }
                })
            }
        }
    }
}

export default rule
