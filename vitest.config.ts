import { defineConfig } from "vitest/config"

export default defineConfig({
    test: {
        include: ["src/eslint/**/*.test.ts", "src/prettier/**/*.test.ts"],
        environment: "node"
    }
})
