import { dirname, resolve } from "node:path"

export interface IEntityFile {
    moduleName: string
    rest: string
    isShared: boolean
    isIndex: boolean
    inInterfaces: boolean
    inTypes: boolean
}

const ENTITY_FILE = /(?:^|\/)src\/entities\/([^/]+)\/(.+)$/

export function normalizePath(filename: string): string {
    return filename.replace(/\\/g, "/")
}

/**
 * Specs and `__tests__` are exempt inside every rule except `require-entity-spec`,
 * which exists to police those paths.
 */
export function isTestFile(filename: string): boolean {
    const path = normalizePath(filename)

    return path.includes("/__tests__/") || path.endsWith(".spec.ts") || path.endsWith(".test.ts")
}

export function locateEntityFile(filename: string): IEntityFile | null {
    const path = normalizePath(filename)
    const match = ENTITY_FILE.exec(path)
    if (!match) return null

    const moduleName = match[1]
    const rest = match[2]

    return {
        moduleName,
        rest,
        isShared: moduleName === "@shared",
        isIndex: rest === "index.ts",
        inInterfaces: rest.startsWith("interfaces/"),
        inTypes: rest.startsWith("types/")
    }
}

/** Entity source under `src/entities/`, excluding specs and `__tests__`. */
export function entitySource(filename: string): IEntityFile | null {
    if (isTestFile(filename)) return null

    return locateEntityFile(filename)
}

export function entityIndex(filename: string): IEntityFile | null {
    const located = entitySource(filename)
    if (!located?.isIndex || located.isShared) return null

    return located
}

export function resolveSpecifier(filename: string, specifier: string): string {
    if (specifier.startsWith(".")) return normalizePath(resolve(dirname(filename), specifier))

    return normalizePath(specifier)
}

export function moduleFromResolved(resolved: string): string | null {
    const match = /(?:^|\/)src\/entities\/([^/]+)(?:\/|$)/.exec(normalizePath(resolved))

    return match?.[1] ?? null
}

export function isAuthPath(resolved: string): boolean {
    return /(?:^|\/)src\/auth(?:\/|$)/.test(normalizePath(resolved))
}

export function isSharedTypesPath(resolved: string): boolean {
    return /(?:^|\/)src\/entities\/@shared\/types(?:\/|$)/.test(normalizePath(resolved))
}

export function isEntityTypesPath(resolved: string): boolean {
    const match = /(?:^|\/)src\/entities\/([^/]+)\/types(?:\/|$)/.exec(normalizePath(resolved))

    return match !== null && match[1] !== "@shared"
}

export function facadePathFor(filename: string): string | null {
    const path = normalizePath(filename)
    const marker = "/src/entities/"
    const index = path.lastIndexOf(marker)

    if (index >= 0) return `${path.slice(0, index)}/src/bling.ts`

    const relative = path.indexOf("src/entities/")
    if (relative < 0) return null

    return `${path.slice(0, relative)}src/bling.ts`
}

export function isTypeModuleFile(located: IEntityFile): boolean {
    return /^types\/[^/]+\.type\.ts$/.test(located.rest)
}
