import type { TSESLint, TSESTree } from "@typescript-eslint/utils"
import { AST_NODE_TYPES } from "@typescript-eslint/utils"
import { propertyName } from "./ast"
import { entityIndex } from "./paths"
import { repositoryCallMethod } from "./repository"

type IMessageIds = "argument" | "endpoint" | "id" | "body" | "missing" | "unexpected" | "option"

const METHODS_WITH_ID = new Set(["show", "update", "replace", "destroy"])
const METHODS_WITH_BODY = new Set(["store", "update", "replace"])
const OPTIONAL_KEYS = new Set(["params", "headers", "shouldIncludeHeadersInResponse", "preserveRedirect"])

/**
 * Entity methods pass a single object literal. `endpoint` is a path string or a
 * template that interpolates an id. Resource identity goes in `id` for
 * show/update/replace/destroy, and in the endpoint template for store actions.
 * The path text itself is the API's, including segments that are not kebab-case.
 */
const rule: TSESLint.RuleModule<IMessageIds> = {
    meta: {
        type: "problem",
        docs: {
            description: "require repository calls to use the endpoint, id, and body shapes entity methods already use"
        },
        messages: {
            argument:
                "Pass one object literal to this.repository.{{method}}. Entity methods pass endpoint, and id or body, as properties of that object.",
            endpoint:
                "endpoint must be a string literal or a template that only interpolates an id (`${id}` or `${params.idCampo}`). " +
                "Do not concatenate the path or pass it through a variable. The path text can be whatever the API documents.",
            id:
                "id must be String(id), String(params.idCampo), an empty string for a bulk or id-less action, " +
                "a path template such as `${id}/situacoes`, or a string field such as params.chaveAcesso.",
            body: "body must be the request object, an identifier such as body, or a member such as parameters.body.",
            missing: "{{method}} requires {{option}}.",
            unexpected:
                "{{method}} does not take {{option}}. index and store put extra path segments in endpoint; show and destroy do not send a body.",
            option: "{{option}} must be {{expected}}."
        },
        schema: []
    },
    defaultOptions: [],
    create(context) {
        if (!entityIndex(context.filename)) return {}

        function isMemberChain(node: TSESTree.Node): boolean {
            if (node.type !== AST_NODE_TYPES.MemberExpression || node.computed || node.optional) return false
            if (node.property.type !== AST_NODE_TYPES.Identifier) return false
            if (node.object.type === AST_NODE_TYPES.Identifier) return true

            return isMemberChain(node.object)
        }

        function isIdReference(node: TSESTree.Node): boolean {
            return node.type === AST_NODE_TYPES.Identifier || isMemberChain(node)
        }

        function isStringCall(node: TSESTree.Node): boolean {
            if (node.type !== AST_NODE_TYPES.CallExpression) return false
            if (node.callee.type !== AST_NODE_TYPES.Identifier || node.callee.name !== "String") return false
            if (node.arguments.length !== 1) return false

            const argument = node.arguments[0]
            if (!argument) return false
            if (argument.type === AST_NODE_TYPES.Literal && argument.value === "") return true

            return isIdReference(argument)
        }

        function isEndpoint(node: TSESTree.Node): boolean {
            if (node.type === AST_NODE_TYPES.Literal && typeof node.value === "string") return node.value.length > 0
            if (node.type !== AST_NODE_TYPES.TemplateLiteral) return false
            if (!node.expressions.every(isIdReference)) return false

            const hasStatic = node.quasis.some(quasi => (quasi.value.cooked ?? quasi.value.raw).length > 0)

            return hasStatic || node.expressions.length > 0
        }

        function isIdValue(node: TSESTree.Node): boolean {
            if (node.type === AST_NODE_TYPES.Literal && node.value === "") return true
            if (isStringCall(node)) return true
            if (isMemberChain(node)) return true
            if (node.type !== AST_NODE_TYPES.TemplateLiteral) return false

            return node.expressions.every(expression => isIdReference(expression) || isStringCall(expression))
        }

        function isBody(node: TSESTree.Node): boolean {
            return (
                node.type === AST_NODE_TYPES.Identifier ||
                node.type === AST_NODE_TYPES.ObjectExpression ||
                isMemberChain(node)
            )
        }

        return {
            CallExpression(node) {
                const method = repositoryCallMethod(node)
                if (!method) return

                const argument = node.arguments[0]
                if (node.arguments.length !== 1 || !argument || argument.type !== AST_NODE_TYPES.ObjectExpression) {
                    context.report({ node, messageId: "argument", data: { method } })
                    return
                }

                const properties = new Map<string, TSESTree.Property>()
                for (const property of argument.properties) {
                    if (property.type !== AST_NODE_TYPES.Property || property.computed) {
                        context.report({ node: property, messageId: "argument", data: { method } })
                        return
                    }

                    const name = propertyName(property.key)
                    if (!name) {
                        context.report({ node: property, messageId: "argument", data: { method } })
                        return
                    }

                    properties.set(name, property)
                }

                const requiresId = METHODS_WITH_ID.has(method)
                const requiresBody = METHODS_WITH_BODY.has(method)

                for (const name of properties.keys()) {
                    const allowed =
                        name === "endpoint" ||
                        OPTIONAL_KEYS.has(name) ||
                        (name === "id" && requiresId) ||
                        (name === "body" && requiresBody)
                    if (allowed) continue

                    context.report({
                        node: properties.get(name) ?? argument,
                        messageId: "unexpected",
                        data: { method, option: name }
                    })
                }

                const endpoint = properties.get("endpoint")
                if (!endpoint) {
                    context.report({ node: argument, messageId: "missing", data: { method, option: "endpoint" } })
                } else if (!isEndpoint(endpoint.value)) {
                    context.report({ node: endpoint.value, messageId: "endpoint" })
                }

                const id = properties.get("id")
                if (requiresId && !id) {
                    context.report({ node: argument, messageId: "missing", data: { method, option: "id" } })
                } else if (id && requiresId && !isIdValue(id.value)) {
                    context.report({ node: id.value, messageId: "id" })
                }

                const body = properties.get("body")
                if (requiresBody && !body) {
                    context.report({ node: argument, messageId: "missing", data: { method, option: "body" } })
                } else if (body && requiresBody && !isBody(body.value)) {
                    context.report({ node: body.value, messageId: "body" })
                }

                for (const name of ["params", "headers"] as const) {
                    const property = properties.get(name)
                    if (!property || property.value.type === AST_NODE_TYPES.ObjectExpression) continue

                    context.report({
                        node: property.value,
                        messageId: "option",
                        data: { option: name, expected: "an object literal" }
                    })
                }

                for (const name of ["shouldIncludeHeadersInResponse", "preserveRedirect"] as const) {
                    const property = properties.get(name)
                    if (!property) continue
                    if (property.value.type === AST_NODE_TYPES.Literal && property.value.value === true) continue

                    context.report({
                        node: property.value,
                        messageId: "option",
                        data: { option: name, expected: "true" }
                    })
                }
            }
        }
    }
}

export default rule
