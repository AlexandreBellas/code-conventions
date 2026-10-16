import type { TSESLint, TSESTree } from "@typescript-eslint/utils"
import { AST_NODE_TYPES } from "@typescript-eslint/utils"
import { commentTarget } from "./ast"
import { entityIndex } from "./paths"

type IMessageIds = "missingSee"

function hasBlingSee(comments: TSESTree.Comment[]): boolean {
    return comments.some(
        comment =>
            comment.type === "Block" &&
            comment.value.includes("@see") &&
            comment.value.includes("developer.bling.com.br")
    )
}

function isTopLevelClass(node: TSESTree.ClassDeclaration): boolean {
    const parent = node.parent
    if (!parent) return false
    if (parent.type === AST_NODE_TYPES.Program) return true

    return (
        parent.type === AST_NODE_TYPES.ExportNamedDeclaration ||
        parent.type === AST_NODE_TYPES.ExportDefaultDeclaration
    )
}

/**
 * Source JSDoc is the review contract for verb and path. Published `.d.ts` strips comments,
 * so a missing `@see` only helps the next editor catch a wrong endpoint before it lands.
 */
const rule: TSESLint.RuleModule<IMessageIds> = {
    meta: {
        type: "problem",
        docs: {
            description: "require a developer.bling.com.br @see on each entity class and method"
        },
        messages: {
            missingSee:
                "{{name}} has no @see link to developer.bling.com.br. " +
                "Copy a sibling method's @see URL so the verb and path can be checked."
        },
        schema: []
    },
    defaultOptions: [],
    create(context) {
        if (!entityIndex(context.filename)) return {}

        return {
            ClassDeclaration(node) {
                if (!isTopLevelClass(node)) return
                if (hasBlingSee(context.sourceCode.getCommentsBefore(commentTarget(node)))) return

                context.report({
                    node,
                    messageId: "missingSee",
                    data: { name: node.id?.name ?? "class" }
                })
            },
            MethodDefinition(node) {
                if (node.kind === "constructor") return

                const body = node.parent
                if (body?.type !== AST_NODE_TYPES.ClassBody) return

                const klass = body.parent
                if (klass?.type !== AST_NODE_TYPES.ClassDeclaration || !isTopLevelClass(klass)) return
                if (hasBlingSee(context.sourceCode.getCommentsBefore(node))) return

                const name = node.key.type === AST_NODE_TYPES.Identifier ? node.key.name : "method"

                context.report({ node, messageId: "missingSee", data: { name } })
            }
        }
    }
}

export default rule
