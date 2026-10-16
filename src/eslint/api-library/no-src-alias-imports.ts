import type { TSESLint, TSESTree } from "@typescript-eslint/utils"
import { isTestFile } from "./paths"

type IMessageIds = "srcAlias"

const SRC_ALIAS = /^src\//

type ISourceNode = TSESTree.ImportDeclaration | TSESTree.ExportAllDeclaration | TSESTree.ExportNamedDeclaration

/**
 * `tsc` does not rewrite `src/...` specifiers. They leak into published `.d.ts` and fail for consumers.
 */
const rule: TSESLint.RuleModule<IMessageIds> = {
    meta: {
        type: "problem",
        docs: {
            description: "disallow src/ import specifiers that tsc would publish unchanged"
        },
        messages: {
            srcAlias:
                'Import "{{source}}" is not rewritten by tsc and leaks into published .d.ts. ' +
                "Use a relative import such as ../@shared/entity."
        },
        schema: []
    },
    defaultOptions: [],
    create(context) {
        if (isTestFile(context.filename)) return {}

        function check(node: ISourceNode, source: string | null): void {
            if (source === null || !SRC_ALIAS.test(source)) return

            context.report({
                node: node.source ?? node,
                messageId: "srcAlias",
                data: { source }
            })
        }

        return {
            ImportDeclaration(node) {
                check(node, node.source.value)
            },
            ExportAllDeclaration(node) {
                check(node, node.source.value)
            },
            ExportNamedDeclaration(node) {
                check(node, node.source?.value ?? null)
            }
        }
    }
}

export default rule
