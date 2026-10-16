import { createRuleTester } from "./rule-tester"
import rule from "./no-raw-date-in-repository"

const ruleTester = createRuleTester()
const filename = "/proj/src/entities/produtos/index.ts"

function method(body: string): string {
    return `class Produtos { async get() { return await this.repository.index({ endpoint: 'produtos', ${body} }) } }`
}

ruleTester.run("no-raw-date-in-repository", rule, {
    valid: [
        {
            name: "wrapped date",
            filename,
            code: method("params: { dataInicial: this.prepareStringOrDateParam(params?.dataInicial) }")
        },
        {
            name: "wrapped datetime",
            filename,
            code: method("params: { dataInclusaoInicial: this.prepareStringOrDateParam(params?.dataInclusaoInicial, true) }")
        },
        {
            name: "non-date key",
            filename,
            code: method("params: { situacao: params?.situacao, databaseId: params?.databaseId }")
        },
        {
            name: "date field on the body is not a query",
            filename,
            code: method("body: { dataEmissao: params?.dataEmissao }")
        },
        {
            name: "interface file is out of scope",
            filename: "/proj/src/entities/produtos/interfaces/get.interface.ts",
            code: method("params: { dataInicial: params?.dataInicial }")
        }
    ],
    invalid: [
        {
            name: "bare date field",
            filename,
            code: method("params: { dataInicial: params?.dataInicial }"),
            errors: [{ messageId: "rawDate", data: { name: "dataInicial" } }]
        },
        {
            name: "date constructed inline",
            filename,
            code: method("params: { data: new Date() }"),
            errors: [{ messageId: "rawDate", data: { name: "data" } }]
        },
        {
            name: "iso string",
            filename,
            code: method("params: { dataEmissaoFinal: params?.dataEmissaoFinal.toISOString() }"),
            errors: [{ messageId: "rawDate", data: { name: "dataEmissaoFinal" } }]
        }
    ]
})
