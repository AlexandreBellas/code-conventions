import { createRuleTester } from "./rule-tester"
import rule from "./enforce-entity-extends-base"

const ruleTester = createRuleTester()
const filename = "/proj/src/entities/contatos/index.ts"

ruleTester.run("enforce-entity-extends-base", rule, {
    valid: [
        {
            name: "extends Entity",
            filename,
            code: "export class Contatos extends Entity {}"
        },
        {
            name: "interface file is out of scope",
            filename: "/proj/src/entities/contatos/interfaces/get.interface.ts",
            code: "export class Contatos {}"
        },
        {
            name: "shared entity file is exempt",
            filename: "/proj/src/entities/@shared/entity.ts",
            code: "export abstract class Entity {}"
        }
    ],
    invalid: [
        {
            name: "missing base class",
            filename,
            code: "export class Contatos {}",
            errors: [{ messageId: "mustExtendEntity", data: { name: "Contatos" } }]
        },
        {
            name: "extends a different class",
            filename,
            code: "export class Contatos extends Other {}",
            errors: [{ messageId: "mustExtendEntity" }]
        },
        {
            name: "two classes",
            filename,
            code: "export class Contatos extends Entity {}\nexport class Extra extends Entity {}",
            errors: [{ messageId: "classCount", data: { count: "2" } }]
        },
        {
            name: "no class",
            filename,
            code: "export const value = 1",
            errors: [{ messageId: "classCount", data: { count: "0" } }]
        }
    ]
})
