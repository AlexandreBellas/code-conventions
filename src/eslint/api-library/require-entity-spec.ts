import type { TSESLint } from "@typescript-eslint/utils"
import { existsSync } from "node:fs"
import { basename, dirname, join } from "node:path"
import { locateEntityFile, normalizePath } from "./paths"

type IMessageIds = "missingSpec" | "strayResponseFixture"

function isResponseFixture(filename: string): boolean {
    return basename(normalizePath(filename)).endsWith("-response.ts")
}

/**
 * A resource with no `__tests__/index.spec.ts` ships endpoint wiring Jest never calls.
 * A `*-response.ts` fixture outside `__tests__/` is compiled into published `lib/`.
 */
const rule: TSESLint.RuleModule<IMessageIds> = {
    meta: {
        type: "problem",
        docs: {
            description: "require entity specs and keep response fixtures inside __tests__"
        },
        messages: {
            missingSpec:
                '{{module}} has no __tests__/index.spec.ts, so its endpoint wiring is never called. ' +
                "Colocate the spec at __tests__/index.spec.ts.",
            strayResponseFixture:
                '"{{name}}" sits outside __tests__ and is published in lib/. Move response fixtures under __tests__/.'
        },
        schema: []
    },
    defaultOptions: [],
    create(context) {
        const located = locateEntityFile(context.filename)
        if (!located) return {}

        return {
            Program(node) {
                if (located.isIndex && !located.isShared) {
                    const spec = join(dirname(context.filename), "__tests__", "index.spec.ts")
                    if (!existsSync(spec)) {
                        context.report({
                            node,
                            messageId: "missingSpec",
                            data: { module: located.moduleName }
                        })
                    }
                }

                if (!isResponseFixture(context.filename)) return
                if (normalizePath(context.filename).includes("/__tests__/")) return

                context.report({
                    node,
                    messageId: "strayResponseFixture",
                    data: { name: basename(normalizePath(context.filename)) }
                })
            }
        }
    }
}

export default rule
