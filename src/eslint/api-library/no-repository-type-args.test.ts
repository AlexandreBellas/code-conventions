import { createRuleTester } from "./rule-tester"
import rule from "./no-repository-type-args"

const ruleTester = createRuleTester()
const filename = "/proj/src/entities/contatos/index.ts"

ruleTester.run("no-repository-type-args", rule, {
    valid: [
        {
            name: "repository call without a type argument",
            filename,
            code: "class Contatos { async find() { return await this.repository.show({ endpoint: 'contatos', id: '1' }) } }"
        },
        {
            name: "interface file is out of scope",
            filename: "/proj/src/entities/contatos/interfaces/find.interface.ts",
            code: "class Contatos { async find() { return await this.repository.show<IFindResponse>({ endpoint: 'contatos' }) } }"
        }
    ],
    invalid: [
        {
            name: "explicit repository type argument",
            filename,
            code: "class Contatos { async find(): Promise<IFindResponse> { return await this.repository.show<IFindResponse>({ endpoint: 'contatos', id: '1' }) } }",
            errors: [{ messageId: "repositoryTypeArg" }]
        }
    ]
})
