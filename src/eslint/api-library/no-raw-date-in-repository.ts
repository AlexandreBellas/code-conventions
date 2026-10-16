import type { TSESLint } from "@typescript-eslint/utils"
import { AST_NODE_TYPES } from "@typescript-eslint/utils"
import { propertyName } from "./ast"
import { entityIndex } from "./paths"
import { isPrepareDateCall, objectProperty, repositoryCallMethod } from "./repository"

type IMessageIds = "rawDate"

const DATE_QUERY_KEY = /^data(?:[A-Z]|$)/

/**
 * `IDefaultParams` allows `Date`, and axios JSON turns a bare date into ISO.
 * Bling list filters expect `YYYY-M-D` from `prepareStringOrDateParam`.
 */
const rule: TSESLint.RuleModule<IMessageIds> = {
    meta: {
        type: "problem",
        docs: {
            description: "require prepareStringOrDateParam on date query fields"
        },
        messages: {
            rawDate:
                'Query field "{{name}}" is sent without this.prepareStringOrDateParam, so a Date becomes an ISO string and the list filter misses rows. ' +
                "Wrap the value in this.prepareStringOrDateParam(value), and pass true when Bling expects a datetime."
        },
        schema: []
    },
    defaultOptions: [],
    create(context) {
        if (!entityIndex(context.filename)) return {}

        return {
            CallExpression(node) {
                if (!repositoryCallMethod(node)) return

                const argument = node.arguments[0]
                if (!argument || argument.type !== AST_NODE_TYPES.ObjectExpression) return

                const params = objectProperty(argument, "params")
                if (!params || params.value.type !== AST_NODE_TYPES.ObjectExpression) return

                for (const prop of params.value.properties) {
                    if (prop.type !== AST_NODE_TYPES.Property || prop.computed) continue

                    const name = propertyName(prop.key)
                    if (!name || !DATE_QUERY_KEY.test(name) || isPrepareDateCall(prop.value)) continue

                    context.report({
                        node: prop,
                        messageId: "rawDate",
                        data: { name }
                    })
                }
            }
        }
    }
}

export default rule
