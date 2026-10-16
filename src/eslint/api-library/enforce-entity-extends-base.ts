import type { TSESLint, TSESTree } from "@typescript-eslint/utils"
import { AST_NODE_TYPES } from "@typescript-eslint/utils"
import { entityIndex } from "./paths"

type IMessageIds = "classCount" | "mustExtendEntity"

function classDeclarations(program: TSESTree.Program): TSESTree.ClassDeclaration[] {
    const classes: TSESTree.ClassDeclaration[] = []

    for (const statement of program.body) {
        if (statement.type === AST_NODE_TYPES.ClassDeclaration) classes.push(statement)

        if (
            statement.type === AST_NODE_TYPES.ExportNamedDeclaration &&
            statement.declaration?.type === AST_NODE_TYPES.ClassDeclaration
        )
            classes.push(statement.declaration)

        if (
            statement.type === AST_NODE_TYPES.ExportDefaultDeclaration &&
            statement.declaration.type === AST_NODE_TYPES.ClassDeclaration
        )
            classes.push(statement.declaration)
    }

    return classes
}

/**
 * `BlingBase.getModule` constructs `new EntityClass(repository)`.
 * A class that does not extend `Entity` has no injected repository.
 */
const rule: TSESLint.RuleModule<IMessageIds> = {
    meta: {
        type: "problem",
        docs: {
            description: "require each entity module to export one class that extends Entity"
        },
        messages: {
            classCount:
                "This module exports {{count}} classes. Export exactly one class with `export class Foo extends Entity` " +
                "so getModule can construct it with the repository.",
            mustExtendEntity:
                "{{name}} must extend Entity (`export class {{name}} extends Entity`) so it receives IBlingRepository and prepareStringOrDateParam."
        },
        schema: []
    },
    defaultOptions: [],
    create(context) {
        if (!entityIndex(context.filename)) return {}

        return {
            Program(node) {
                const classes = classDeclarations(node)
                if (classes.length !== 1) {
                    context.report({
                        node,
                        messageId: "classCount",
                        data: { count: String(classes.length) }
                    })
                    return
                }

                const declaration = classes[0]
                const superClass = declaration.superClass
                if (superClass?.type === AST_NODE_TYPES.Identifier && superClass.name === "Entity") return

                context.report({
                    node: declaration,
                    messageId: "mustExtendEntity",
                    data: { name: declaration.id?.name ?? "class" }
                })
            }
        }
    }
}

export default rule
