import type { TSESLint, TSESTree } from "@typescript-eslint/utils"
import { AST_NODE_TYPES } from "@typescript-eslint/utils"
import { basename } from "node:path"
import { entitySource, normalizePath } from "./paths"

type IMessageIds =
    | "typeOrInterfaceInIndex"
    | "helperInIndex"
    | "runtimeInTypeFiles"
    | "wrongTypeFolderSuffix"
    | "missingRequiredType"
    | "missingRequiredInterface"

interface IExportedNames {
    interfaces: Set<string>
    aliases: Set<string>
}

/**
 * `situacao.type.ts` must export `type ISituacao`. `get.interface.ts` must export
 * `interface IGetResponse`. Kebab segments map to PascalCase: `any-name-here` → `AnyNameHere`.
 */
function requiredExportName(filename: string, kind: "type" | "interface"): string | null {
    const base = basename(normalizePath(filename))
    const suffix = kind === "type" ? ".type.ts" : ".interface.ts"
    if (!base.endsWith(suffix)) return null

    const stem = base.slice(0, -suffix.length)
    const pascal = stem
        .split("-")
        .filter(segment => segment.length > 0)
        .map(segment => segment.charAt(0).toUpperCase() + segment.slice(1))
        .join("")

    if (pascal.length === 0) return null

    return kind === "type" ? `I${pascal}` : `I${pascal}Response`
}

function collectExports(program: TSESTree.Program): IExportedNames {
    const localInterfaces = new Set<string>()
    const localAliases = new Set<string>()

    for (const statement of program.body) {
        const declaration =
            statement.type === AST_NODE_TYPES.ExportNamedDeclaration ? statement.declaration : statement
        if (!declaration) continue
        if (declaration.type === AST_NODE_TYPES.TSInterfaceDeclaration) localInterfaces.add(declaration.id.name)
        if (declaration.type === AST_NODE_TYPES.TSTypeAliasDeclaration) localAliases.add(declaration.id.name)
    }

    const interfaces = new Set<string>()
    const aliases = new Set<string>()

    for (const statement of program.body) {
        if (statement.type !== AST_NODE_TYPES.ExportNamedDeclaration) continue

        const declaration = statement.declaration
        if (declaration?.type === AST_NODE_TYPES.TSInterfaceDeclaration) interfaces.add(declaration.id.name)
        if (declaration?.type === AST_NODE_TYPES.TSTypeAliasDeclaration) aliases.add(declaration.id.name)

        for (const specifier of statement.specifiers) {
            if (specifier.local.type !== AST_NODE_TYPES.Identifier) continue
            if (specifier.exported.type !== AST_NODE_TYPES.Identifier) continue

            const local = specifier.local.name
            const exported = specifier.exported.name
            if (localInterfaces.has(local)) interfaces.add(exported)
            if (localAliases.has(local)) aliases.add(exported)
        }
    }

    return { interfaces, aliases }
}

function isInsideClass(node: TSESTree.Node): boolean {
    let current = node.parent
    while (current) {
        if (current.type === AST_NODE_TYPES.ClassBody) return true
        current = current.parent
    }

    return false
}

function isNestedFunction(node: TSESTree.Node): boolean {
    let current = node.parent
    while (current) {
        if (
            current.type === AST_NODE_TYPES.FunctionDeclaration ||
            current.type === AST_NODE_TYPES.FunctionExpression ||
            current.type === AST_NODE_TYPES.ArrowFunctionExpression
        ) {
            return true
        }
        current = current.parent
    }

    return false
}

/**
 * The entity class lives in `index.ts`. Request and response types live in
 * `interfaces/<operation>.interface.ts` and `types/<name>.type.ts`, and each of
 * those files exports the name its filename implies. An operation file may
 * export that response as an interface or as a type alias. Empty responses use
 * `void` or `null`, as in `type IDeleteResponse = void` and `type IChangePasswordResponse = null`.
 */
const rule: TSESLint.RuleModule<IMessageIds> = {
    meta: {
        type: "problem",
        docs: {
            description:
                "keep the entity class in index.ts, and require each type file to export the name its filename implies"
        },
        messages: {
            typeOrInterfaceInIndex:
                "Do not declare an interface or type alias in the entity index. " +
                "Put them in interfaces/<operation>.interface.ts or types/<name>.type.ts and leave index.ts to the class.",
            helperInIndex:
                "Do not declare a function beside the entity class. index.ts exports the class; keep behavior in class methods.",
            runtimeInTypeFiles:
                "interfaces/ and types/ cannot contain functions or classes, because the build publishes that runtime in lib/. " +
                "Keep only export type and export interface in those folders.",
            wrongTypeFolderSuffix:
                '"{{name}}" is in the wrong type folder. Files under interfaces/ must be *.interface.ts and files under types/ must be *.type.ts.',
            missingRequiredType:
                "{{file}} must export at least type {{name}}. The filename stem is the type name: situacao.type.ts exports type ISituacao.",
            missingRequiredInterface:
                "{{file}} must export at least interface {{name}} or type {{name}}. " +
                "Use a type when the response is void or null, as in type IDeleteResponse = void or type IChangePasswordResponse = null. " +
                "get.interface.ts exports interface IGetResponse, and any-name-here.interface.ts exports interface IAnyNameHereResponse."
        },
        schema: []
    },
    defaultOptions: [],
    create(context) {
        const located = entitySource(context.filename)
        if (!located || located.isShared) return {}

        const inTypeFolder = located.inInterfaces || located.inTypes

        const reportRuntime = (node: TSESTree.Node): void => {
            if (!inTypeFolder) return

            context.report({ node, messageId: "runtimeInTypeFiles" })
        }

        const reportHelper = (node: TSESTree.Node): void => {
            if (!located.isIndex || isInsideClass(node) || isNestedFunction(node)) return

            context.report({ node, messageId: "helperInIndex" })
        }

        return {
            TSInterfaceDeclaration(node) {
                if (located.isIndex) context.report({ node, messageId: "typeOrInterfaceInIndex" })
            },
            TSTypeAliasDeclaration(node) {
                if (located.isIndex) context.report({ node, messageId: "typeOrInterfaceInIndex" })
            },
            FunctionDeclaration(node) {
                if (inTypeFolder) reportRuntime(node)
                reportHelper(node)
            },
            FunctionExpression(node) {
                if (inTypeFolder) reportRuntime(node)
                reportHelper(node)
            },
            ArrowFunctionExpression(node) {
                if (inTypeFolder) reportRuntime(node)
                reportHelper(node)
            },
            ClassDeclaration(node) {
                if (located.isIndex) return

                reportRuntime(node)
            },
            ClassExpression: reportRuntime,
            Program(node) {
                if (!inTypeFolder) return

                const file = basename(normalizePath(context.filename))
                const wrongInterface = located.inInterfaces && !file.endsWith(".interface.ts")
                const wrongType = located.inTypes && !file.endsWith(".type.ts")
                if (wrongInterface || wrongType) {
                    context.report({
                        node,
                        messageId: "wrongTypeFolderSuffix",
                        data: { name: file }
                    })
                    return
                }

                const exported = collectExports(node)
                const requiredType = located.inTypes ? requiredExportName(context.filename, "type") : null
                const requiredInterface = located.inInterfaces
                    ? requiredExportName(context.filename, "interface")
                    : null

                if (requiredType && !exported.aliases.has(requiredType)) {
                    context.report({
                        node,
                        messageId: "missingRequiredType",
                        data: { file, name: requiredType }
                    })
                }

                const hasRequiredInterface =
                    requiredInterface !== null &&
                    (exported.interfaces.has(requiredInterface) || exported.aliases.has(requiredInterface))
                if (requiredInterface && !hasRequiredInterface) {
                    context.report({
                        node,
                        messageId: "missingRequiredInterface",
                        data: { file, name: requiredInterface }
                    })
                }
            }
        }
    }
}

export default rule
