import type { TSESLint, TSESTree } from "@typescript-eslint/utils"
import { AST_NODE_TYPES } from "@typescript-eslint/utils"
import { parse } from "@typescript-eslint/typescript-estree"
import { readFileSync } from "node:fs"
import { traverse } from "./ast"
import { entityIndex, facadePathFor } from "./paths"

type IMessageIds = "unregistered"

const facadeNames = new Map<string, Set<string> | null>()

function registeredClasses(facadeFile: string): Set<string> | null {
    if (facadeNames.has(facadeFile)) return facadeNames.get(facadeFile) ?? null

    let source: string
    try {
        source = readFileSync(facadeFile, "utf8")
    } catch {
        facadeNames.set(facadeFile, null)
        return null
    }

    try {
        const program = parse(source, { loc: false, range: false, jsx: false })
        const names = new Set<string>()

        traverse(program, node => {
            if (node.type !== AST_NODE_TYPES.CallExpression) return

            const callee = node.callee
            const isGetModule =
                (callee.type === AST_NODE_TYPES.Identifier && callee.name === "getModule") ||
                (callee.type === AST_NODE_TYPES.MemberExpression &&
                    !callee.computed &&
                    callee.property.type === AST_NODE_TYPES.Identifier &&
                    callee.property.name === "getModule")

            if (!isGetModule) return

            const argument = node.arguments[0]
            if (argument?.type === AST_NODE_TYPES.Identifier) names.add(argument.name)
        })

        facadeNames.set(facadeFile, names)
        return names
    } catch {
        facadeNames.set(facadeFile, null)
        return null
    }
}

function exportedClassNames(program: TSESTree.Program): string[] {
    const names: string[] = []

    for (const statement of program.body) {
        if (
            statement.type === AST_NODE_TYPES.ExportNamedDeclaration &&
            statement.declaration?.type === AST_NODE_TYPES.ClassDeclaration &&
            statement.declaration.id
        )
            names.push(statement.declaration.id.name)

        if (
            statement.type === AST_NODE_TYPES.ExportDefaultDeclaration &&
            statement.declaration.type === AST_NODE_TYPES.ClassDeclaration &&
            statement.declaration.id
        )
            names.push(statement.declaration.id.name)
    }

    return names
}

/**
 * `Bling.create` only exposes getters on `BlingBase`. A class the facade never references
 * compiles into `lib/` and consumers still cannot call the resource.
 */
const rule: TSESLint.RuleModule<IMessageIds> = {
    meta: {
        type: "problem",
        docs: {
            description: "require each entity class to be passed to getModule in src/bling.ts"
        },
        messages: {
            unregistered:
                "{{className}} is never passed to getModule in src/bling.ts, so consumers cannot call it. " +
                "Import the class and add a getter on BlingBase."
        },
        schema: []
    },
    defaultOptions: [],
    create(context) {
        if (!entityIndex(context.filename)) return {}

        return {
            Program(node) {
                const facade = facadePathFor(context.filename)
                const registered = facade === null ? null : registeredClasses(facade)

                for (const className of exportedClassNames(node)) {
                    if (registered?.has(className)) continue

                    context.report({
                        node,
                        messageId: "unregistered",
                        data: { className }
                    })
                }
            }
        }
    }
}

export default rule
