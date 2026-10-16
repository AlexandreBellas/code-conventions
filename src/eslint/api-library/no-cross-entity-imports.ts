import type { TSESLint, TSESTree } from "@typescript-eslint/utils"
import { entitySource, moduleFromResolved, resolveSpecifier } from "./paths"

type IMessageIds = "crossEntityImport"

type ISourceNode = TSESTree.ImportDeclaration | TSESTree.ExportAllDeclaration | TSESTree.ExportNamedDeclaration

/**
 * One resource's types must not type another. The NFS-e bug imported NFC-e's `ISendResponse`
 * through `../nfces/...`, which has no `/entities/` segment in the raw specifier.
 */
const rule: TSESLint.RuleModule<IMessageIds> = {
    meta: {
        type: "problem",
        docs: {
            description: "disallow imports that resolve into a different entity module"
        },
        messages: {
            crossEntityImport:
                'This import resolves into entity "{{module}}", so one resource\'s types silently type another. ' +
                "Use a local ./interfaces or ./types file, or move a truly shared union to @shared/types."
        },
        schema: []
    },
    defaultOptions: [],
    create(context) {
        const located = entitySource(context.filename)
        if (!located) return {}

        const currentModule = located.moduleName

        function check(node: ISourceNode, specifier: string | null): void {
            if (specifier === null) return

            const imported = moduleFromResolved(resolveSpecifier(context.filename, specifier))
            if (imported === null || imported === currentModule || imported === "@shared") return

            context.report({
                node: node.source ?? node,
                messageId: "crossEntityImport",
                data: { module: imported }
            })
        }

        return {
            ImportDeclaration(node) {
                check(node, node.source.value)
            },
            ExportAllDeclaration(node) {
                check(node, node.source.value)
            },
            ExportNamedDeclaration(node) {
                check(node, node.source?.value ?? null)
            }
        }
    }
}

export default rule
