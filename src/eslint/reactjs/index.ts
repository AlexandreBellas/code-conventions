import type { TSESLint } from "@typescript-eslint/utils"
import enforceComponentImportLimits from "./enforce-component-import-limits"
import enforceComponentStructure from "./enforce-component-structure"
import enforceConstantLocation from "./enforce-constant-location"
import enforceHelperLocation from "./enforce-helper-location"
import enforcePropsTypeNaming from "./enforce-props-type-naming"
import enforceQueryHooksStructure from "./enforce-query-hooks-structure"
import enforceRefSuffix from "./enforce-ref-suffix"
import enforceServiceStructure from "./enforce-service-structure"
import enforceSingleComponent from "./enforce-single-component"
import enforceSingleUseTranslation from "./enforce-single-use-translation"
import enforceTestColocation from "./enforce-test-colocation"
import enforceTypeLocation from "./enforce-type-location"
import enforceUnionTypesSourceOfTruth from "./enforce-union-types-source-of-truth"
import maxComponentLines from "./max-component-lines"
import noDirectServiceCalls from "./no-direct-service-calls"
import noNonNullAssertion from "./no-non-null-assertion"
import noRawButton from "./no-raw-button"
import noRestrictedAxiosAuth from "./no-restricted-axios-auth"
import noRestrictedUseContext from "./no-restricted-use-context"
import noTypesInConstants from "./no-types-in-constants"
import regionOrdering from "./region-ordering"
import requireRegions from "./require-regions"
import requireStorybook from "./require-storybook"
import requireUtilsConventions from "./require-utils-conventions"
import useeffect from "./useeffect"


const rules = {
    "enforce-component-structure": enforceComponentStructure,
    "enforce-constant-location": enforceConstantLocation,
    "enforce-component-import-limits": enforceComponentImportLimits,
    "enforce-helper-location": enforceHelperLocation,
    "enforce-test-colocation": enforceTestColocation,
    "enforce-query-hooks-structure": enforceQueryHooksStructure,
    "enforce-props-type-naming": enforcePropsTypeNaming,
    "enforce-ref-suffix": enforceRefSuffix,
    "enforce-single-component": enforceSingleComponent,
    "enforce-single-use-translation": enforceSingleUseTranslation,
    "enforce-type-location": enforceTypeLocation,
    "enforce-union-types-source-of-truth": enforceUnionTypesSourceOfTruth,
    "max-component-lines": maxComponentLines,
    "no-direct-service-calls": noDirectServiceCalls,
    "no-non-null-assertion": noNonNullAssertion,
    "no-raw-button": noRawButton,
    "no-restricted-axios-auth": noRestrictedAxiosAuth,
    "no-restricted-use-context": noRestrictedUseContext,
    "no-types-in-constants": noTypesInConstants,
    "region-ordering": regionOrdering,
    "require-utils-conventions": requireUtilsConventions,
    "require-regions": requireRegions,
    "require-storybook": requireStorybook,
    "enforce-service-structure": enforceServiceStructure,
    useeffect: useeffect
} satisfies Record<string, TSESLint.LooseRuleDefinition>

const reactjsPlugin = {
    rules,
    configs: {
        recommended: [] as TSESLint.FlatConfig.ConfigArray
    }
} satisfies TSESLint.FlatConfig.Plugin

const ctxProviderGlobs = [
    "src/**/contexts/*/index.tsx",
    "src/**/contexts/*/hooks/use*.ts",
    "src/**/contexts/*Provider.tsx"
] as const


/**
 * Files that carry no product code: tests exercise the product, and `docs/` holds Storybook
 * support material. Every rule below spreads these into its `ignores`, except
 * `enforce-test-colocation`, whose whole job is to police where test files sit.
 */
const testAndDocsGlobs = [
    "src/**/*.test.ts",
    "src/**/*.test.tsx",
    "src/**/*.spec.ts",
    "src/**/*.spec.tsx",
    "src/**/docs/**"
] as const

reactjsPlugin.configs.recommended = [
    { plugins: { reactjs: reactjsPlugin } },
    {
        files: ["src/**/*.tsx", "src/**/hooks/**/*.ts"],
        ignores: [...testAndDocsGlobs, "src/**/hooks/queries/**", "src/**/hooks/services/**"],
        rules: {
            "reactjs/no-direct-service-calls": "error"
        }
    },
    {
        files: ["src/**/*.tsx", "src/**/hooks/use*.ts", "src/**/use*/index.ts"],
        ignores: [...testAndDocsGlobs, "src/**/*.stories.tsx", "src/hooks/queries/**"],
        rules: {
            "reactjs/enforce-props-type-naming": "error"
        }
    },
    {
        files: ["src/**/*.tsx"],
        ignores: [...testAndDocsGlobs, "src/**/*.stories.tsx"],
        rules: {
            "reactjs/max-component-lines": "error"
        }
    },
    {
        files: ["src/**/*.tsx"],
        ignores: [...testAndDocsGlobs, "src/**/*.stories.tsx", "src/components/ui/**", "src/routes/**"],
        rules: {
            "reactjs/enforce-single-component": "error"
        }
    },
    {
        files: ["src/components/**/*.tsx", "src/pages/**/*.tsx"],
        ignores: [...testAndDocsGlobs, "src/components/**/*.stories.tsx", "src/components/ui/**", "src/pages/**/*.stories.tsx"],
        rules: {
            "reactjs/enforce-component-import-limits": "error"
        }
    },
    {
        files: ["src/**/*.tsx", "src/**/*.ts"],
        ignores: [...testAndDocsGlobs, "src/**/utils/**/*.ts", "src/**/*.stories.tsx", "src/routes/**"],
        rules: {
            "reactjs/enforce-helper-location": "error"
        }
    },
    {
        files: ["src/**/*.tsx", "src/**/*.ts"],
        ignores: [...testAndDocsGlobs,
            "src/**/constants/**/*.ts",
            "src/**/@types/**",
            "src/**/*.stories.tsx",
            "src/routes/**",
            "src/hooks/queries/**",
            "src/utils/**"
        ],
        rules: {
            "reactjs/enforce-constant-location": "error"
        }
    },
    {
        files: [...testAndDocsGlobs, "src/**/constants/**/*.ts"],
        rules: {
            "reactjs/no-types-in-constants": "error"
        }
    },
    {
        files: [...testAndDocsGlobs, "src/**/utils/**/*.ts"],
        rules: {
            "reactjs/require-utils-conventions": "error"
        }
    },
    {
        files: [...testAndDocsGlobs, "src/**/@types/**/*.ts"],
        rules: {
            "reactjs/enforce-union-types-source-of-truth": "error"
        }
    },
    {
        files: ["src/**/*.tsx", "src/**/hooks/**/use*.ts", "src/**/hooks/*/index.ts"],
        ignores: [...testAndDocsGlobs, "src/**/*.stories.tsx", "src/components/ui/**", "src/routes/**"],
        rules: {
            "reactjs/region-ordering": "error",
            "reactjs/require-regions": "error",
            "reactjs/enforce-type-location": "error",
            "reactjs/useeffect": "error"
        }
    },
    {
        files: ["src/**/*.ts", "src/**/*.tsx"],
        ignores: [...testAndDocsGlobs, "src/**/*.stories.tsx", "src/**/*.stories.ts"],
        rules: {
            "reactjs/no-non-null-assertion": "error",
            "reactjs/no-restricted-axios-auth": "error"
        }
    },
    {
        files: ["src/**/*.tsx"],
        ignores: [...testAndDocsGlobs, "src/**/*.stories.tsx", "src/components/ui/**"],
        rules: {
            "reactjs/no-raw-button": "error"
        }
    },
    {
        files: ["src/**/*.ts", "src/**/*.tsx"],
        ignores: [...testAndDocsGlobs, "src/**/*.stories.tsx", "src/**/*.stories.ts", ...ctxProviderGlobs],
        rules: {
            "reactjs/no-restricted-use-context": "error"
        }
    },
    {
        files: ["src/components/**/*.tsx"],
        ignores: [...testAndDocsGlobs, "src/components/ui/**", ...ctxProviderGlobs],
        rules: {
            "reactjs/require-storybook": "warn"
        }
    },
    {
        files: ["src/components/**/*.ts", "src/components/**/*.tsx", "src/pages/**/*.ts", "src/pages/**/*.tsx"],
        ignores: [...testAndDocsGlobs, "src/components/ui/**"],
        rules: {
            "reactjs/enforce-component-structure": "error"
        }
    },
    {
        files: ["src/**/hooks/**/*.ts", "src/**/*.tsx"],
        ignores: [...testAndDocsGlobs, "src/**/*.stories.tsx", "src/**/*.stories.ts"],
        rules: {
            "reactjs/enforce-single-use-translation": "error"
        }
    },
    {
        files: ["src/**/*.ts", "src/**/*.tsx"],
        ignores: [...testAndDocsGlobs, "src/**/*.stories.tsx", "src/**/*.stories.ts"],
        rules: {
            "reactjs/enforce-query-hooks-structure": "error"
        }
    },
    {
        files: ["src/**/*.tsx", "src/**/hooks/**/use*.ts", "src/**/hooks/*/index.ts"],
        ignores: [...testAndDocsGlobs, "src/**/*.stories.tsx", "src/**/*.stories.ts"],
        rules: {
            "reactjs/enforce-ref-suffix": "error"
        }
    },
    {
        files: ["src/services/**/*.ts", "src/services/**/*.tsx"],
        ignores: [...testAndDocsGlobs],
        rules: {
            "reactjs/enforce-service-structure": "error"
        }
    },
    {
        files: ["**/*.ts", "**/*.tsx", "**/*.js", "**/*.jsx"],
        rules: {
            "reactjs/enforce-test-colocation": "error"
        }
    }
]

export default reactjsPlugin
