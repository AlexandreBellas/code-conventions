import { createRuleTester } from "./rule-tester"
import rule from "./require-entity-jsdoc-see"

const ruleTester = createRuleTester()
const filename = "/proj/src/entities/contatos/index.ts"
const see = "/**\n * Recurso.\n * @see https://developer.bling.com.br/referencia#/Contatos\n */"

ruleTester.run("require-entity-jsdoc-see", rule, {
    valid: [
        {
            name: "class and method both link to Bling",
            filename,
            code: `${see}\nexport class Contatos extends Entity {\n${see}\nasync get() { return await this.repository.index({ endpoint: 'contatos' }) }\n}`
        },
        {
            name: "homologacao anchor still counts",
            filename: "/proj/src/entities/homologacao/index.ts",
            code: "/** @see https://developer.bling.com.br/homologacao */\nexport class Homologacao extends Entity {\n/** @see https://developer.bling.com.br/homologacao */\nasync execute() { return null }\n}"
        },
        {
            name: "interface file is out of scope",
            filename: "/proj/src/entities/contatos/interfaces/get.interface.ts",
            code: "export class Contatos { async get() { return null } }"
        }
    ],
    invalid: [
        {
            name: "method has no see",
            filename,
            code: `${see}\nexport class Contatos extends Entity {\nasync get() { return null }\n}`,
            errors: [{ messageId: "missingSee", data: { name: "get" } }]
        },
        {
            name: "class has no see",
            filename,
            code: `export class Contatos extends Entity {\n${see}\nasync get() { return null }\n}`,
            errors: [{ messageId: "missingSee", data: { name: "Contatos" } }]
        },
        {
            name: "see without the Bling host",
            filename,
            code: "/** @see https://example.com */\nexport class Contatos extends Entity {\n/** @see https://developer.bling.com.br/referencia */\nasync get() { return null }\n}",
            errors: [{ messageId: "missingSee", data: { name: "Contatos" } }]
        }
    ]
})
