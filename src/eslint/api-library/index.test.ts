import { describe, expect, it } from "vitest"
import plugin from "./index"

const ruleNames = [
    "enforce-entity-extends-base",
    "enforce-entity-module-layout",
    "enforce-repository-call",
    "enforce-type-export-style",
    "no-auth-imports-in-entities",
    "no-bracket-query-names",
    "no-cross-entity-imports",
    "no-http-in-entities",
    "no-raw-date-in-repository",
    "no-repository-type-args",
    "no-src-alias-imports",
    "require-entity-jsdoc-see",
    "require-entity-spec",
    "require-facade-registration",
    "require-forwarded-params"
]

describe("api-library plugin", () => {
    it("registers every rule", () => {
        expect(Object.keys(plugin.rules ?? {}).sort()).toEqual([...ruleNames].sort())
    })

    it("recommended config uses the api-library prefix", () => {
        const ids = plugin.configs.recommended.flatMap(config => Object.keys(config.rules ?? {}))

        expect(ids).toContain("api-library/no-cross-entity-imports")
        expect(ids).toContain("api-library/require-forwarded-params")
        expect(ids.every(id => id.startsWith("api-library/"))).toBe(true)
        expect(plugin.configs.recommended[0]?.plugins).toHaveProperty("api-library")
        expect(plugin.configs.recommended[0]?.plugins).not.toHaveProperty("reactjs")
    })
})
