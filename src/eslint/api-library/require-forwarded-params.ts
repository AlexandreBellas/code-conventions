import type { TSESLint, TSESTree } from "@typescript-eslint/utils"
import { AST_NODE_TYPES } from "@typescript-eslint/utils"
import { parse } from "@typescript-eslint/typescript-estree"
import { existsSync, readFileSync } from "node:fs"
import { join } from "node:path"
import { isParamRoot, propertyName, traverse } from "./ast"
import { entityIndex, resolveSpecifier } from "./paths"
import { repositoryCallMethod } from "./repository"

type IMessageIds = "droppedParam"

interface IParam {
    name: string | null
    pattern: TSESTree.ObjectPattern | null
    typeName: string
    node: TSESTree.Node
}

const parsedFiles = new Map<string, TSESTree.Program | null>()

function loadProgram(filePath: string): TSESTree.Program | null {
    if (parsedFiles.has(filePath)) return parsedFiles.get(filePath) ?? null

    try {
        const program = parse(readFileSync(filePath, "utf8"), { loc: false, range: false, jsx: false })
        parsedFiles.set(filePath, program)
        return program
    } catch {
        parsedFiles.set(filePath, null)
        return null
    }
}

function resolveModuleFile(filename: string, specifier: string): string | null {
    const resolved = resolveSpecifier(filename, specifier)
    const candidates = [resolved, `${resolved}.ts`, `${resolved}.tsx`, join(resolved, "index.ts")]

    for (const candidate of candidates) if (existsSync(candidate)) return candidate

    return null
}

function importedSpecifier(program: TSESTree.Program, typeName: string): string | null {
    for (const statement of program.body) {
        if (statement.type !== AST_NODE_TYPES.ImportDeclaration) continue

        for (const specifier of statement.specifiers) {
            if (specifier.type !== AST_NODE_TYPES.ImportSpecifier) continue
            if (specifier.imported.type !== AST_NODE_TYPES.Identifier) continue
            if (specifier.imported.name === typeName) return statement.source.value
        }
    }

    return null
}

function findInterface(program: TSESTree.Program, name: string): TSESTree.TSInterfaceDeclaration | null {
    for (const statement of program.body) {
        if (statement.type === AST_NODE_TYPES.TSInterfaceDeclaration && statement.id.name === name) return statement

        if (
            statement.type === AST_NODE_TYPES.ExportNamedDeclaration &&
            statement.declaration?.type === AST_NODE_TYPES.TSInterfaceDeclaration &&
            statement.declaration.id.name === name
        )
            return statement.declaration
    }

    return null
}

function propertiesOf(program: TSESTree.Program, name: string, seen: Set<string>): string[] {
    if (seen.has(name)) return []
    seen.add(name)

    const declaration = findInterface(program, name)
    if (!declaration) return []

    const names: string[] = []

    for (const heritage of declaration.extends ?? []) {
        if (heritage.expression.type !== AST_NODE_TYPES.Identifier) continue

        names.push(...propertiesOf(program, heritage.expression.name, seen))
    }

    for (const member of declaration.body.body) {
        if (
            member.type !== AST_NODE_TYPES.TSPropertySignature &&
            member.type !== AST_NODE_TYPES.TSMethodSignature
        )
            continue
        if (member.computed) continue

        const property = propertyName(member.key)
        if (property) names.push(property)
    }

    const unique: string[] = []
    const used = new Set<string>()
    for (const property of names) {
        if (used.has(property)) continue
        used.add(property)
        unique.push(property)
    }

    return unique
}

function interfaceProperties(filename: string, program: TSESTree.Program, typeName: string): string[] | null {
    const specifier = importedSpecifier(program, typeName)
    if (!specifier) return null

    const file = resolveModuleFile(filename, specifier)
    if (!file) return null

    const parsed = loadProgram(file)
    if (!parsed || !findInterface(parsed, typeName)) return null

    return propertiesOf(parsed, typeName, new Set())
}

function typeReferenceName(node: TSESTree.TypeNode | undefined): string | null {
    if (!node || node.type !== AST_NODE_TYPES.TSTypeReference) return null
    if (node.typeName.type !== AST_NODE_TYPES.Identifier) return null

    return node.typeName.name
}

function readParameter(param: TSESTree.Parameter): IParam | null {
    if (param.type === AST_NODE_TYPES.Identifier) {
        const typeName = typeReferenceName(param.typeAnnotation?.typeAnnotation)
        if (!typeName) return null

        return { name: param.name, pattern: null, typeName, node: param }
    }

    if (param.type === AST_NODE_TYPES.AssignmentPattern && param.left.type === AST_NODE_TYPES.Identifier) {
        const typeName = typeReferenceName(param.left.typeAnnotation?.typeAnnotation)
        if (!typeName) return null

        return { name: param.left.name, pattern: null, typeName, node: param }
    }

    if (param.type === AST_NODE_TYPES.ObjectPattern) {
        const typeName = typeReferenceName(param.typeAnnotation?.typeAnnotation)
        if (!typeName) return null

        return { name: null, pattern: param, typeName, node: param }
    }

    return null
}

function isParamInit(node: TSESTree.Node | null | undefined, paramName: string | null): boolean {
    if (!node) return false
    if (isParamRoot(node, paramName)) return true
    if (node.type === AST_NODE_TYPES.LogicalExpression) return isParamRoot(node.left, paramName)

    return false
}

function addObjectPattern(pattern: TSESTree.ObjectPattern, names: Set<string>): void {
    for (const property of pattern.properties) {
        if (property.type !== AST_NODE_TYPES.Property || property.computed) continue

        const name = propertyName(property.key)
        if (name) names.add(name)
    }
}

function collectReferences(
    root: TSESTree.Node,
    paramName: string | null,
    pattern: TSESTree.ObjectPattern | null
): { names: Set<string>; forwardsAll: boolean } {
    const names = new Set<string>()
    let forwardsAll = false
    if (pattern) addObjectPattern(pattern, names)

    traverse(root, node => {
        if (
            node.type === AST_NODE_TYPES.MemberExpression &&
            !node.computed &&
            isParamRoot(node.object, paramName)
        ) {
            const name = propertyName(node.property)
            if (name) names.add(name)
        }

        if (
            node.type === AST_NODE_TYPES.VariableDeclarator &&
            node.id.type === AST_NODE_TYPES.ObjectPattern &&
            isParamInit(node.init, paramName)
        )
            addObjectPattern(node.id, names)

        if (node.type === AST_NODE_TYPES.SpreadElement && isParamRoot(node.argument, paramName)) forwardsAll = true

        if (node.type !== AST_NODE_TYPES.CallExpression || !repositoryCallMethod(node)) return

        const argument = node.arguments[0]
        if (!argument || argument.type !== AST_NODE_TYPES.ObjectExpression) return

        for (const property of argument.properties) {
            if (property.type === AST_NODE_TYPES.SpreadElement && isParamRoot(property.argument, paramName))
                forwardsAll = true

            if (property.type !== AST_NODE_TYPES.Property || property.computed) continue
            if (isParamRoot(property.value, paramName)) forwardsAll = true
            if (propertyName(property.key) !== "params" || property.value.type !== AST_NODE_TYPES.ObjectExpression)
                continue

            for (const inner of property.value.properties) {
                if (inner.type === AST_NODE_TYPES.SpreadElement && isParamRoot(inner.argument, paramName))
                    forwardsAll = true
                if (inner.type !== AST_NODE_TYPES.Property || inner.computed) continue

                const name = propertyName(inner.key)
                if (name) names.add(name)
            }
        }
    })

    return { names, forwardsAll }
}

/**
 * A property on a params interface that the method never reads is omitted from the query string.
 * Callers pass the filter and Bling returns an unfiltered page.
 */
const rule: TSESLint.RuleModule<IMessageIds> = {
    meta: {
        type: "problem",
        docs: {
            description: "require every property of a single params interface to be forwarded"
        },
        messages: {
            droppedParam:
                'Property "{{name}}" of {{typeName}} is never forwarded, so the filter is omitted from the query string. ' +
                "Add it to the repository params object, or use it in id or endpoint when it is a path segment."
        },
        schema: []
    },
    defaultOptions: [],
    create(context) {
        if (!entityIndex(context.filename)) return {}

        return {
            MethodDefinition(node) {
                if (node.kind === "constructor" || node.value.type !== AST_NODE_TYPES.FunctionExpression) return

                for (const param of node.value.params) {
                    const read = readParameter(param)
                    if (!read) continue

                    const properties = interfaceProperties(context.filename, context.sourceCode.ast, read.typeName)
                    if (!properties || properties.length === 0) continue

                    const references = collectReferences(node.value, read.name, read.pattern)
                    if (references.forwardsAll) continue

                    for (const name of properties) {
                        if (references.names.has(name)) continue

                        context.report({
                            node: read.node,
                            messageId: "droppedParam",
                            data: { name, typeName: read.typeName }
                        })
                    }
                }
            }
        }
    }
}

export default rule
