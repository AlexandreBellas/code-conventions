import { defineConfig } from "tsup"

export default defineConfig({
    entry: {
        "eslint/index": "src/eslint/index.ts"
    },
    format: ["esm"],
    dts: {
        compilerOptions: {
            // tsup forces baseUrl: "." for DTS; silence TS 6 deprecation until tsup stops.
            ignoreDeprecations: "6.0"
        }
    },
    clean: true,
    sourcemap: true,
    target: "node18",
    outDir: "dist",
    external: [
        "eslint",
        "@typescript-eslint/utils",
        "@typescript-eslint/typescript-estree"
    ]
})
