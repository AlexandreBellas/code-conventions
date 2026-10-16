import { join } from "node:path"
import { vi } from "vitest"
import { createRuleTester } from "./rule-tester"

const { existing } = vi.hoisted(() => ({ existing: new Set<string>() }))

vi.mock("node:fs", () => {
    const existsSync = (filePath: string) => existing.has(filePath)
    const mock = { existsSync }

    return { default: mock, ...mock }
})

import rule from "./require-entity-spec"

const ruleTester = createRuleTester()
const indexFile = "/proj/src/entities/contatos/index.ts"

existing.add(join("/proj/src/entities/contatos", "__tests__", "index.spec.ts"))

ruleTester.run("require-entity-spec", rule, {
    valid: [
        {
            name: "spec is colocated",
            filename: indexFile,
            code: "export class Contatos extends Entity {}"
        },
        {
            name: "response fixture inside __tests__",
            filename: "/proj/src/entities/contatos/__tests__/find-response.ts",
            code: "export const findResponse = {}"
        },
        {
            name: "spec file itself",
            filename: "/proj/src/entities/contatos/__tests__/index.spec.ts",
            code: "export const value = 1"
        },
        {
            name: "shared entity file",
            filename: "/proj/src/entities/@shared/entity.ts",
            code: "export abstract class Entity {}"
        },
        {
            name: "spec outside __tests__ is not a publish bug",
            filename: "/proj/src/bling.spec.ts",
            code: "export const value = 1"
        }
    ],
    invalid: [
        {
            name: "missing spec",
            filename: "/proj/src/entities/produtos/index.ts",
            code: "export class Produtos extends Entity {}",
            errors: [{ messageId: "missingSpec", data: { module: "produtos" } }]
        },
        {
            name: "response fixture outside __tests__",
            filename: "/proj/src/entities/contatos/find-response.ts",
            code: "export const findResponse = {}",
            errors: [{ messageId: "strayResponseFixture", data: { name: "find-response.ts" } }]
        }
    ]
})
