import type { TSESLint } from "@typescript-eslint/utils"
import enforceEntityExtendsBase from "./enforce-entity-extends-base"
import enforceEntityModuleLayout from "./enforce-entity-module-layout"
import enforceRepositoryCall from "./enforce-repository-call"
import enforceTypeExportStyle from "./enforce-type-export-style"
import noAuthImportsInEntities from "./no-auth-imports-in-entities"
import noBracketQueryNames from "./no-bracket-query-names"
import noCrossEntityImports from "./no-cross-entity-imports"
import noHttpInEntities from "./no-http-in-entities"
import noRawDateInRepository from "./no-raw-date-in-repository"
import noRepositoryTypeArgs from "./no-repository-type-args"
import noSrcAliasImports from "./no-src-alias-imports"
import requireEntityJsdocSee from "./require-entity-jsdoc-see"
import requireEntitySpec from "./require-entity-spec"
import requireFacadeRegistration from "./require-facade-registration"
import requireForwardedParams from "./require-forwarded-params"

const rules = {
    "enforce-entity-extends-base": enforceEntityExtendsBase,
    "enforce-entity-module-layout": enforceEntityModuleLayout,
    "enforce-repository-call": enforceRepositoryCall,
    "enforce-type-export-style": enforceTypeExportStyle,
    "no-auth-imports-in-entities": noAuthImportsInEntities,
    "no-bracket-query-names": noBracketQueryNames,
    "no-cross-entity-imports": noCrossEntityImports,
    "no-http-in-entities": noHttpInEntities,
    "no-raw-date-in-repository": noRawDateInRepository,
    "no-repository-type-args": noRepositoryTypeArgs,
    "no-src-alias-imports": noSrcAliasImports,
    "require-entity-jsdoc-see": requireEntityJsdocSee,
    "require-entity-spec": requireEntitySpec,
    "require-facade-registration": requireFacadeRegistration,
    "require-forwarded-params": requireForwardedParams
} satisfies Record<string, TSESLint.LooseRuleDefinition>

const apiLibraryPlugin = {
    rules,
    configs: {
        recommended: [] as TSESLint.FlatConfig.ConfigArray
    }
} satisfies TSESLint.FlatConfig.Plugin

const testGlobs = ["src/**/*.spec.ts", "src/**/*.test.ts", "src/**/__tests__/**"] as const

const entityFiles = ["src/entities/**/*.ts"] as const

apiLibraryPlugin.configs.recommended = [
    { plugins: { "api-library": apiLibraryPlugin } },
    {
        files: [...entityFiles],
        ignores: [...testGlobs],
        rules: {
            "api-library/no-http-in-entities": "error",
            "api-library/no-cross-entity-imports": "error",
            "api-library/enforce-repository-call": "error",
            "api-library/no-raw-date-in-repository": "error",
            "api-library/enforce-entity-module-layout": "error",
            "api-library/enforce-entity-extends-base": "error",
            "api-library/enforce-type-export-style": "error",
            "api-library/require-entity-jsdoc-see": "error",
            "api-library/no-repository-type-args": "error",
            "api-library/no-auth-imports-in-entities": "error",
            "api-library/no-bracket-query-names": "error"
        }
    },
    {
        files: ["src/**/*.ts"],
        ignores: [...testGlobs],
        rules: {
            "api-library/no-src-alias-imports": "error"
        }
    },
    {
        files: [...entityFiles],
        rules: {
            "api-library/require-entity-spec": "error"
        }
    },
    {
        files: ["src/entities/*/index.ts"],
        ignores: ["src/entities/@shared/**"],
        rules: {
            "api-library/require-facade-registration": "error",
            "api-library/require-forwarded-params": "error"
        }
    }
]

export default apiLibraryPlugin
