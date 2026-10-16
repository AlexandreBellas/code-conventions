import { mkdirSync, mkdtempSync, rmSync, writeFileSync } from "node:fs"
import { tmpdir } from "node:os"
import { join } from "node:path"
import { afterAll } from "vitest"
import { createRuleTester } from "./rule-tester"
import rule from "./require-facade-registration"

const root = mkdtempSync(join(tmpdir(), "facade-"))
const entity = join(root, "src/entities/contatos")
mkdirSync(entity, { recursive: true })
writeFileSync(
    join(root, "src/bling.ts"),
    "export class BlingBase { get contatos() { return this.getModule(Contatos) } }\n"
)

afterAll(() => {
    rmSync(root, { recursive: true, force: true })
})

const ruleTester = createRuleTester()
const filename = join(entity, "index.ts")

ruleTester.run("require-facade-registration", rule, {
    valid: [
        {
            name: "class is passed to getModule",
            filename,
            code: "export class Contatos extends Entity {}"
        },
        {
            name: "shared file is out of scope",
            filename: join(root, "src/entities/@shared/entity.ts"),
            code: "export abstract class Entity {}"
        },
        {
            name: "interface file is out of scope",
            filename: join(entity, "interfaces/get.interface.ts"),
            code: "export class Contatos {}"
        }
    ],
    invalid: [
        {
            name: "class is imported but never passed to getModule",
            filename,
            code: "export class Produtos extends Entity {}",
            errors: [{ messageId: "unregistered", data: { className: "Produtos" } }]
        }
    ]
})
