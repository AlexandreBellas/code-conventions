import { createRuleTester } from "./rule-tester"
import rule from "./no-bracket-query-names"

const ruleTester = createRuleTester()
const filename = "/proj/src/entities/produtos/interfaces/get.interface.ts"

ruleTester.run("no-bracket-query-names", rule, {
    valid: [
        {
            name: "array property without brackets in the name",
            filename,
            code: "export interface IGetParams { idsProdutos?: number[] }"
        },
        {
            name: "object key without brackets",
            filename: "/proj/src/entities/produtos/index.ts",
            code: "const params = { idsProdutos: ids }"
        },
        {
            name: "spec file",
            filename: "/proj/src/entities/produtos/__tests__/index.spec.ts",
            code: "const params = { 'idsProdutos[]': [1] }"
        }
    ],
    invalid: [
        {
            name: "interface property already ends with brackets",
            filename,
            code: "export interface IGetParams { 'idsProdutos[]'?: number[] }",
            errors: [{ messageId: "bracketQueryName", data: { name: "idsProdutos[]" } }]
        },
        {
            name: "object key already ends with brackets",
            filename: "/proj/src/entities/produtos/index.ts",
            code: "const params = { 'idsProdutos[]': ids }",
            errors: [{ messageId: "bracketQueryName", data: { name: "idsProdutos[]" } }]
        }
    ]
})
