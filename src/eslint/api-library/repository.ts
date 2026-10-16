import type { TSESTree } from "@typescript-eslint/utils"
import { AST_NODE_TYPES } from "@typescript-eslint/utils"
import { propertyName } from "./ast"

const REPOSITORY_METHODS = new Set(["index", "show", "store", "update", "replace", "destroy"])

export function repositoryCallMethod(node: TSESTree.CallExpression): string | null {
    const callee = node.callee
    if (callee.type !== AST_NODE_TYPES.MemberExpression || callee.computed) return null
    if (callee.property.type !== AST_NODE_TYPES.Identifier) return null
    if (!REPOSITORY_METHODS.has(callee.property.name)) return null

    const object = callee.object
    if (object.type !== AST_NODE_TYPES.MemberExpression || object.computed) return null
    if (object.property.type !== AST_NODE_TYPES.Identifier || object.property.name !== "repository") return null
    if (object.object.type !== AST_NODE_TYPES.ThisExpression) return null

    return callee.property.name
}

export function objectProperty(
    object: TSESTree.ObjectExpression,
    name: string
): TSESTree.Property | undefined {
    for (const prop of object.properties) {
        if (prop.type !== AST_NODE_TYPES.Property || prop.computed) continue
        if (propertyName(prop.key) === name) return prop
    }

    return undefined
}

export function isPrepareDateCall(node: TSESTree.Node): boolean {
    if (node.type !== AST_NODE_TYPES.CallExpression) return false

    const callee = node.callee
    if (callee.type !== AST_NODE_TYPES.MemberExpression || callee.computed) return false
    if (callee.property.type !== AST_NODE_TYPES.Identifier) return false
    if (callee.property.name !== "prepareStringOrDateParam") return false

    return callee.object.type === AST_NODE_TYPES.ThisExpression
}
