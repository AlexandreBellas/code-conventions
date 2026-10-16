import type { TSESLint, TSESTree } from "@typescript-eslint/utils"
import { propertyName } from "./ast"
import { entitySource } from "./paths"

type IMessageIds = "bracketQueryName"

function reportBracket(context: TSESLint.RuleContext<IMessageIds, []>, node: TSESTree.Node, key: TSESTree.Node): void {
    const name = propertyName(key)
    if (!name?.endsWith("[]")) return

    context.report({
        node,
        messageId: "bracketQueryName",
        data: { name }
    })
}

/**
 * Axios appends `[]` when the value is an array. A property already named `idsProdutos[]`
 * is sent as `idsProdutos[][]`, and Bling drops the filter.
 */
const rule: TSESLint.RuleModule<IMessageIds> = {
    meta: {
        type: "problem",
        docs: {
            description: "disallow query names that already end with []"
        },
        messages: {
            bracketQueryName:
                'Query name "{{name}}" already ends with []. Axios appends [] again for arrays, and Bling drops the filter. ' +
                "Remove the brackets and type the value as an array."
        },
        schema: []
    },
    defaultOptions: [],
    create(context) {
        if (!entitySource(context.filename)) return {}

        return {
            Property(node) {
                if (node.computed) return

                reportBracket(context, node, node.key)
            },
            TSPropertySignature(node) {
                if (node.computed) return

                reportBracket(context, node, node.key)
            },
            PropertyDefinition(node) {
                if (node.computed) return

                reportBracket(context, node, node.key)
            },
            MethodDefinition(node) {
                if (node.computed) return

                reportBracket(context, node, node.key)
            }
        }
    }
}

export default rule
