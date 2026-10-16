import type { TSESLint, TSESTree } from "@typescript-eslint/utils"
import { AST_NODE_TYPES } from "@typescript-eslint/utils"
import {
    entitySource,
    isEntityTypesPath,
    isSharedTypesPath,
    isTypeModuleFile,
    resolveSpecifier
} from "./paths"

type IMessageIds =
    | "sharedTypeMustDefault"
    | "entityTypeMustBeNamed"
    | "sharedTypeDefaultImport"
    | "entityTypeNamedImport"

function typesImportKind(filename: string, specifier: string): "shared" | "entity" | null {
    const resolved = resolveSpecifier(filename, specifier)
    if (isSharedTypesPath(resolved)) return "shared"
    if (isEntityTypesPath(resolved)) return "entity"

    return null
}

interface ITypeFileShape {
    hasDefault: boolean
    hasExportedAlias: boolean
    hasBareAlias: boolean
}

function typeFileShape(program: TSESTree.Program): ITypeFileShape {
    let hasDefault = false
    let hasExportedAlias = false
    let hasBareAlias = false

    for (const statement of program.body) {
        if (statement.type === AST_NODE_TYPES.ExportDefaultDeclaration) hasDefault = true
        if (statement.type === AST_NODE_TYPES.TSTypeAliasDeclaration) hasBareAlias = true

        if (statement.type !== AST_NODE_TYPES.ExportNamedDeclaration) continue

        if (statement.declaration?.type === AST_NODE_TYPES.TSTypeAliasDeclaration) hasExportedAlias = true

        if (statement.exportKind === "type" && statement.specifiers.length > 0) hasExportedAlias = true
    }

    return { hasDefault, hasExportedAlias, hasBareAlias }
}

/**
 * Shared `ISituacao` is `0 | 1` and default-exported. A resource `ISituacao` is a different union
 * and named-exported. Mixing the import styles binds the wrong situation set.
 */
const rule: TSESLint.RuleModule<IMessageIds> = {
    meta: {
        type: "problem",
        docs: {
            description: "require default exports for @shared/types and named exports for entity types"
        },
        messages: {
            sharedTypeMustDefault:
                '@shared/types files declare a type alias and `export default` it. ' +
                'Use `type IFoo = ...` plus `export default IFoo`, with no `export type`, so importers cannot bind the wrong union.',
            entityTypeMustBeNamed:
                "Entity types use `export type IFoo = ...` and no default export, so they stay distinct from the shared 0 | 1 situation type.",
            sharedTypeDefaultImport:
                'Import "{{name}}" from @shared/types with a default import (`import {{name}} from "..."`). ' +
                "A named import can bind a different union than the shared type.",
            entityTypeNamedImport:
                'Import "{{name}}" from this entity\'s types/ with a named import (`import { {{name}} } from "..."`). ' +
                "A default import binds the shared type style, not the resource union."
        },
        schema: []
    },
    defaultOptions: [],
    create(context) {
        const located = entitySource(context.filename)
        if (!located) return {}

        return {
            ImportDeclaration(node) {
                const kind = typesImportKind(context.filename, node.source.value)
                if (!kind) return

                const hasDefault = node.specifiers.some(
                    specifier => specifier.type === AST_NODE_TYPES.ImportDefaultSpecifier
                )
                const hasNamed = node.specifiers.some(specifier => specifier.type === AST_NODE_TYPES.ImportSpecifier)

                if (kind === "shared" && hasNamed) {
                    const named = node.specifiers.find(
                        specifier => specifier.type === AST_NODE_TYPES.ImportSpecifier
                    )
                    const name =
                        named && named.type === AST_NODE_TYPES.ImportSpecifier && named.imported.type === AST_NODE_TYPES.Identifier
                            ? named.imported.name
                            : "type"

                    context.report({ node, messageId: "sharedTypeDefaultImport", data: { name } })
                }

                if (kind === "entity" && hasDefault) {
                    const imported = node.specifiers.find(
                        specifier => specifier.type === AST_NODE_TYPES.ImportDefaultSpecifier
                    )

                    context.report({
                        node,
                        messageId: "entityTypeNamedImport",
                        data: { name: imported && imported.type === AST_NODE_TYPES.ImportDefaultSpecifier ? imported.local.name : "type" }
                    })
                }
            },
            Program(node) {
                if (!isTypeModuleFile(located)) return

                const shape = typeFileShape(node)

                if (located.isShared) {
                    if (!shape.hasDefault || !shape.hasBareAlias || shape.hasExportedAlias)
                        context.report({ node, messageId: "sharedTypeMustDefault" })
                    return
                }

                if (shape.hasDefault || shape.hasBareAlias || !shape.hasExportedAlias)
                    context.report({ node, messageId: "entityTypeMustBeNamed" })
            }
        }
    }
}

export default rule
