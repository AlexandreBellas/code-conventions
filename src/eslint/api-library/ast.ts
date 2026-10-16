import type { TSESTree } from "@typescript-eslint/utils"
import { AST_NODE_TYPES } from "@typescript-eslint/utils"

function isNode(value: unknown): value is TSESTree.Node {
    return typeof value === "object" && value !== null && "type" in value && typeof value.type === "string"
}

export function traverse(node: TSESTree.Node, visit: (node: TSESTree.Node) => void): void {
    visit(node)

    for (const [key, value] of Object.entries(node)) {
        if (key === "parent") continue

        if (Array.isArray(value)) {
            for (const item of value) if (isNode(item)) traverse(item, visit)
            continue
        }

        if (isNode(value)) traverse(value, visit)
    }
}

export function propertyName(key: TSESTree.Node): string | null {
    if (key.type === AST_NODE_TYPES.Identifier) return key.name
    if (key.type === AST_NODE_TYPES.Literal && typeof key.value === "string") return key.value

    return null
}

export function isParamRoot(node: TSESTree.Node | null | undefined, paramName: string | null): boolean {
    if (!node || paramName === null) return false
    if (node.type === AST_NODE_TYPES.Identifier) return node.name === paramName
    if (node.type === AST_NODE_TYPES.ChainExpression) return isParamRoot(node.expression, paramName)
    if (node.type === AST_NODE_TYPES.TSAsExpression) return isParamRoot(node.expression, paramName)
    if (node.type === AST_NODE_TYPES.TSNonNullExpression) return isParamRoot(node.expression, paramName)
    if (node.type === AST_NODE_TYPES.TSSatisfiesExpression) return isParamRoot(node.expression, paramName)
    if (node.type === AST_NODE_TYPES.TSTypeAssertion) return isParamRoot(node.expression, paramName)

    return false
}

export function commentTarget(node: TSESTree.Node): TSESTree.Node {
    let current = node

    while (
        current.parent &&
        (current.parent.type === AST_NODE_TYPES.ExportNamedDeclaration ||
            current.parent.type === AST_NODE_TYPES.ExportDefaultDeclaration) &&
        current.parent.declaration === current
    )
        current = current.parent

    return current
}
