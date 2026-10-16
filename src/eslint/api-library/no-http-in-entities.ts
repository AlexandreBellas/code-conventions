import type { TSESLint, TSESTree } from "@typescript-eslint/utils"
import { AST_NODE_TYPES } from "@typescript-eslint/utils"
import { entitySource } from "./paths"

type IMessageIds = "httpImport" | "fetchCall"

const HTTP_MODULES = new Set([
    "axios",
    "http",
    "https",
    "node:http",
    "node:https",
    "undici",
    "got",
    "node-fetch"
])

function isTypeOnlyImport(node: TSESTree.ImportDeclaration): boolean {
    if (node.importKind === "type") return true

    return (
        node.specifiers.length > 0 &&
        node.specifiers.every(
            specifier => specifier.type === AST_NODE_TYPES.ImportSpecifier && specifier.importKind === "type"
        )
    )
}

function isFetchCallee(node: TSESTree.CallExpression): boolean {
    const callee = node.callee
    if (callee.type === AST_NODE_TYPES.Identifier) return callee.name === "fetch"

    if (callee.type !== AST_NODE_TYPES.MemberExpression || callee.computed) return false
    if (callee.property.type !== AST_NODE_TYPES.Identifier || callee.property.name !== "fetch") return false
    if (callee.object.type !== AST_NODE_TYPES.Identifier) return false

    return callee.object.name === "globalThis" || callee.object.name === "window" || callee.object.name === "global"
}

/**
 * Axios, fetch, or another HTTP client inside an entity skips repository interceptors
 * (auth headers, 401 retry, and `BlingApiException` mapping).
 */
const rule: TSESLint.RuleModule<IMessageIds> = {
    meta: {
        type: "problem",
        docs: {
            description:
                "disallow HTTP clients in entity modules so calls go through this.repository.*"
        },
        messages: {
            httpImport:
                'Importing "{{name}}" from an entity skips repository auth headers, 401 retry, and BlingApiException mapping. ' +
                "Call this.repository.index, show, store, update, replace, or destroy instead.",
            fetchCall:
                "Calling fetch from an entity skips the repository. " +
                "Call this.repository.index, show, store, update, replace, or destroy instead."
        },
        schema: []
    },
    defaultOptions: [],
    create(context) {
        if (!entitySource(context.filename)) return {}

        return {
            ImportDeclaration(node) {
                if (isTypeOnlyImport(node)) return
                if (!HTTP_MODULES.has(node.source.value)) return

                context.report({
                    node,
                    messageId: "httpImport",
                    data: { name: node.source.value }
                })
            },
            ImportExpression(node) {
                if (node.source.type !== AST_NODE_TYPES.Literal || typeof node.source.value !== "string") return
                if (!HTTP_MODULES.has(node.source.value)) return

                context.report({
                    node,
                    messageId: "httpImport",
                    data: { name: node.source.value }
                })
            },
            CallExpression(node) {
                if (!isFetchCallee(node)) return

                context.report({ node, messageId: "fetchCall" })
            }
        }
    }
}

export default rule
