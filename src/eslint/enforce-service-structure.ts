import type { TSESLint } from "@typescript-eslint/utils"
import { ALLOWED_SUBFOLDERS } from "./enforce-component-structure"

type IMessageIds = "componentFolderInService"

/**
 * Normalises a filesystem path to use forward slashes so comparisons
 * work consistently on every OS.
 */
function normalize(filename: string): string {
    return filename.replace(/\\/g, "/")
}

function isExcludedFile(filename: string): boolean {
    return /\.(test|spec|stories)\.[jt]sx?$/.test(normalize(filename))
}

/**
 * Index of the `services` segment that sits directly under `src`, or -1.
 *
 * The `src` neighbour check is what keeps unrelated `services/` segments
 * (`src/hooks/services/`, `src/pages/Foo/services/`) out of scope.
 */
function findServicesIndex(parts: string[]): number {
    for (let i = parts.length - 2; i > 0; i--) if (parts[i] === "services" && parts[i - 1] === "src") return i

    return -1
}

/**
 * Returns the first component-structure folder crossed below `src/services`,
 * or null when every folder in the path describes an implementation variant.
 *
 * The banned set is {@link ALLOWED_SUBFOLDERS}, reused from
 * `enforce-component-structure` on purpose: the two rules must stay in sync, so
 * a folder name that becomes valid for components becomes invalid for services.
 */
function getComponentFolderInServicePath(normalizedPath: string): string | null {
    const parts = normalizedPath.split("/")
    const servicesIdx = findServicesIndex(parts)
    if (servicesIdx === -1) return null

    // The last segment is the file name, so `slice` leaves only directories to check.
    const directories = parts.slice(servicesIdx + 1, -1)

    return directories.find(segment => ALLOWED_SUBFOLDERS.has(segment)) ?? null
}

/**
 * Subfolders under `src/services` name an implementation variant of a gateway
 * (`api/`, `in-memory/`, `websocket/`, `local/`); component scaffolding folders
 * hide helpers, constants and models where sibling implementations of the same
 * gateway — and the rest of the app — cannot reach them, so they get duplicated.
 *
 * Reusable logic belongs on the abstract service class, local logic in the
 * service root file, and anything of wider interest in its global counterpart
 * (`src/utils/`, `src/constants/`, `src/@types/`).
 */
const rule: TSESLint.RuleModule<IMessageIds> = {
    meta: {
        type: "problem",
        docs: {
            description:
                "enforce that subfolders under src/services describe implementation variants only — " +
                "component scaffolding folders belong on the abstract service, the service root file, " +
                "or their global counterpart"
        },
        messages: {
            componentFolderInService:
                'Service file lives in the component-structure folder "{{ folder }}". ' +
                "Subfolders under src/services are reserved for implementation variants (api/, in-memory/, " +
                "websocket/). Move the contents to the abstract service class when reusable, to the service " +
                "root file when local to it, or to the matching global folder (src/utils/, src/constants/, " +
                "src/@types/)."
        },
        schema: []
    },
    defaultOptions: [],
    create(context) {
        if (isExcludedFile(context.filename)) return {}

        const folder = getComponentFolderInServicePath(normalize(context.filename))
        if (!folder) return {}

        return {
            Program(node) {
                context.report({
                    node,
                    messageId: "componentFolderInService",
                    data: { folder }
                })
            }
        }
    }
}

export default rule
export { getComponentFolderInServicePath }
