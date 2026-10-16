import type { TSESLint, TSESTree } from "@typescript-eslint/utils"
import { entitySource, isAuthPath, resolveSpecifier } from "./paths"

type IMessageIds = "authImport"

/**
 * An entity that imports `src/auth` can mint or refresh tokens outside `IAuthProvider`,
 * so the call skips the repository's header and 401-retry path.
 */
const rule: TSESLint.RuleModule<IMessageIds> = {
    meta: {
        type: "problem",
        docs: {
            description: "disallow entity imports that resolve under src/auth"
        },
        messages: {
            authImport:
                "Entities must not import src/auth. Token handling stays in IAuthProvider and OAuthClient. " +
                "Call this.repository.* for resource requests."
        },
        schema: []
    },
    defaultOptions: [],
    create(context) {
        if (!entitySource(context.filename)) return {}

        function check(node: TSESTree.Node, specifier: string | null): void {
            if (specifier === null) return
            if (!isAuthPath(resolveSpecifier(context.filename, specifier))) return

            context.report({ node, messageId: "authImport" })
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
