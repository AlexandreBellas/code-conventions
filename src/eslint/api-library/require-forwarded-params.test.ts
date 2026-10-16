import { mkdirSync, mkdtempSync, rmSync, writeFileSync } from "node:fs"
import { tmpdir } from "node:os"
import { join } from "node:path"
import { afterAll } from "vitest"
import { createRuleTester } from "./rule-tester"
import rule from "./require-forwarded-params"

const root = mkdtempSync(join(tmpdir(), "forwarded-"))
const entity = join(root, "src/entities/contatos")
mkdirSync(join(entity, "interfaces"), { recursive: true })
writeFileSync(
    join(entity, "interfaces/get.interface.ts"),
    [
        "export interface IPageParams { pagina?: number }",
        "export interface IGetParams extends IPageParams { limite?: number; idContato?: number }",
        "export interface IFindParams { idContato: number }"
    ].join("\n")
)
writeFileSync(
    join(entity, "interfaces/create.interface.ts"),
    "export interface ICreateBody { nome: string; codigo: string }"
)
writeFileSync(
    join(entity, "interfaces/update.interface.ts"),
    "export interface IUpdateParams { id: number }\nexport interface IUpdateBody { nome: string }"
)

afterAll(() => {
    rmSync(root, { recursive: true, force: true })
})

const ruleTester = createRuleTester()
const filename = join(entity, "index.ts")

function method(signature: string, body: string): string {
    return `import { IGetParams, IFindParams } from './interfaces/get.interface'
import { ICreateBody } from './interfaces/create.interface'
import { IUpdateParams, IUpdateBody } from './interfaces/update.interface'
export class Contatos extends Entity {
  async run(${signature}) {
    ${body}
  }
}`
}

ruleTester.run("require-forwarded-params", rule, {
    valid: [
        {
            name: "every filter is a repository params key",
            filename,
            code: method(
                "params?: IGetParams",
                `return await this.repository.index({
                  endpoint: 'contatos',
                  params: { pagina: params?.pagina, limite: params?.limite, idContato: params?.idContato }
                })`
            )
        },
        {
            name: "path id counts",
            filename,
            code: method(
                "params: IFindParams",
                "return await this.repository.show({ endpoint: 'contatos', id: String(params.idContato) })"
            )
        },
        {
            name: "endpoint interpolation counts",
            filename,
            code: method(
                "params: IFindParams",
                "return await this.repository.store({ endpoint: `contatos/${params.idContato}/enviar`, body: {} })"
            )
        },
        {
            name: "destructure binding counts",
            filename,
            code: method(
                "params: IFindParams",
                "const { idContato } = params\nreturn await this.repository.show({ endpoint: 'contatos', id: String(idContato) })"
            )
        },
        {
            name: "object spread forwards every property",
            filename,
            code: method(
                "params?: IGetParams",
                "return await this.repository.index({ endpoint: 'contatos', params: { ...params } })"
            )
        },
        {
            name: "intersection of params and body is skipped",
            filename,
            code: method(
                "params: IUpdateParams & IUpdateBody",
                "return await this.repository.update({ endpoint: 'contatos', id: String(params.id), body: { nome: params.nome } })"
            )
        },
        {
            name: "method with no params interface",
            filename,
            code: "export class Homologacao extends Entity { async execute() { return await this.repository.index({ endpoint: 'homologacao' }) } }"
        },
        {
            name: "whole body argument is forwarded",
            filename,
            code: method(
                "body: ICreateBody",
                "return await this.repository.store({ endpoint: 'contatos', body })"
            )
        },
        {
            name: "interface file is out of scope",
            filename: join(entity, "interfaces/get.interface.ts"),
            code: method("params?: IGetParams", "return await this.repository.index({ endpoint: 'contatos', params: {} })")
        }
    ],
    invalid: [
        {
            name: "limite is declared and never read",
            filename,
            code: method(
                "params?: IGetParams",
                "return await this.repository.index({ endpoint: 'contatos', params: { pagina: params?.pagina, idContato: params?.idContato } })"
            ),
            errors: [{ messageId: "droppedParam", data: { name: "limite", typeName: "IGetParams" } }]
        },
        {
            name: "inherited pagina is declared and never read",
            filename,
            code: method(
                "params?: IGetParams",
                "return await this.repository.index({ endpoint: 'contatos', params: { limite: params?.limite, idContato: params?.idContato } })"
            ),
            errors: [{ messageId: "droppedParam", data: { name: "pagina", typeName: "IGetParams" } }]
        },
        {
            name: "body field omitted from the payload",
            filename,
            code: method(
                "body: ICreateBody",
                "return await this.repository.store({ endpoint: 'contatos', body: { nome: body.nome } })"
            ),
            errors: [{ messageId: "droppedParam", data: { name: "codigo", typeName: "ICreateBody" } }]
        }
    ]
})
