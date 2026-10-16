import type { TSESLint } from "@typescript-eslint/utils"
import { entityIndex } from "./paths"
import { repositoryCallMethod } from "./repository"

type IMessageIds = "repositoryTypeArg"

/**
 * `this.repository.show<IFindResponse>(...)` duplicates the method's `Promise<...>`
 * and can disagree with the type consumers actually see.
 */
const rule: TSESLint.RuleModule<IMessageIds> = {
    meta: {
        type: "problem",
        docs: {
            description: "disallow type arguments on repository calls"
        },
        messages: {
            repositoryTypeArg:
                "Do not pass a type argument to this.repository.*. It can disagree with the method's Promise return type. " +
                "Drop the generic and annotate the entity method return type only."
        },
        schema: []
    },
    defaultOptions: [],
    create(context) {
        if (!entityIndex(context.filename)) return {}

        return {
            CallExpression(node) {
                if (!repositoryCallMethod(node)) return
                if (!node.typeArguments || node.typeArguments.params.length === 0) return

                context.report({ node: node.typeArguments, messageId: "repositoryTypeArg" })
            }
        }
    }
}

export default rule
