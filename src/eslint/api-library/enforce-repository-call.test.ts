import { createRuleTester } from "./rule-tester"
import rule from "./enforce-repository-call"

const ruleTester = createRuleTester()
const filename = "/proj/src/entities/contatos/index.ts"

function call(method: string, options: string): string {
    return `class Contatos { async run() { return await this.repository.${method}(${options}) } }`
}

ruleTester.run("enforce-repository-call", rule, {
    valid: [
        { name: "index with a path literal", filename, code: call("index", "{ endpoint: 'contatos' }") },
        {
            name: "index with query params",
            filename,
            code: call("index", "{ endpoint: 'contatos', params: { pagina: params?.pagina } }")
        },
        {
            name: "index that returns headers",
            filename,
            code: call("index", "{ endpoint: 'homologacao/produtos', shouldIncludeHeadersInResponse: true }")
        },
        {
            name: "path text is not required to be kebab-case",
            filename,
            code: call("index", "{ endpoint: 'camposCustomizados' }")
        },
        {
            name: "underscore path literal",
            filename,
            code: call("index", "{ endpoint: 'campos_customizados' }")
        },
        {
            name: "uppercase static path in a template",
            filename,
            code: call("store", "{ endpoint: `NFSe/${id}/enviar`, body }")
        },
        {
            name: "show wraps the id in String",
            filename,
            code: call("show", "{ endpoint: 'contatos', id: String(params.idContato) }")
        },
        {
            name: "show uses a destructured id",
            filename,
            code: call("show", "{ endpoint: 'contatos', id: String(idContato) }")
        },
        {
            name: "show nests the action in id",
            filename,
            code: call("show", "{ endpoint: 'contatos', id: `${params.idContato}/tipos` }")
        },
        {
            name: "show nests several ids in the path",
            filename,
            code: call(
                "show",
                "{ endpoint: 'produtos', id: `${params.idProduto}/lotes/${params.idLote}/depositos/${params.idDeposito}/saldo` }"
            )
        },
        {
            name: "show passes a string field as id",
            filename,
            code: call("show", "{ endpoint: 'nfe/documento', id: params.chaveAcesso, params: { formato: params.formato } }")
        },
        {
            name: "show keeps redirect headers",
            filename,
            code: call(
                "show",
                "{ endpoint: 'documentos-compartilhados', id: String(params.token), headers: { 'x-bling-homologacao': params.hash }, shouldIncludeHeadersInResponse: true, preserveRedirect: true }"
            )
        },
        {
            name: "destroy one resource",
            filename,
            code: call("destroy", "{ endpoint: 'contatos', id: String(params.idContato) }")
        },
        {
            name: "destroy many with an empty id",
            filename,
            code: call("destroy", "{ endpoint: 'produtos', id: '', params: { idsProdutos: params.idsProdutos } }")
        },
        {
            name: "destroy many with String of an empty id",
            filename,
            code: call("destroy", "{ endpoint: 'contatos', id: String(''), params: { idsContatos: params.idsContatos } }")
        },
        {
            name: "store passes body through",
            filename,
            code: call("store", "{ endpoint: 'contatos', body }")
        },
        {
            name: "store sends an object body",
            filename,
            code: call("store", "{ endpoint: `nfce/${params.idNotaFiscalConsumidor}/enviar`, body: {} }")
        },
        {
            name: "store interpolates a nested id and forwards parameters.body",
            filename,
            code: call(
                "store",
                "{ endpoint: `produtos/estruturas/${parameters.params.idProdutoEstrutura}/componentes`, body: parameters.body }"
            )
        },
        {
            name: "update nests the action in id",
            filename,
            code: call("update", "{ endpoint: 'contatos', id: `${idContato}/situacoes`, body }")
        },
        {
            name: "update wraps String inside the id template",
            filename,
            code: call("update", "{ endpoint: 'logisticas', id: `${String(idLogisticaServico)}/situacoes`, body }")
        },
        {
            name: "replace uses String on the destructured id",
            filename,
            code: call("replace", "{ endpoint: 'produtos', id: String(idProduto), body }")
        },
        {
            name: "replace with an empty id",
            filename,
            code: call("replace", "{ endpoint: 'nfse/configuracoes', id: '', body }")
        },
        {
            name: "interface file is out of scope",
            filename: "/proj/src/entities/contatos/interfaces/get.interface.ts",
            code: call("index", "{ endpoint: path }")
        }
    ],
    invalid: [
        {
            name: "endpoint passed as a variable",
            filename,
            code: call("index", "{ endpoint: path }"),
            errors: [{ messageId: "endpoint" }]
        },
        {
            name: "endpoint built with concatenation",
            filename,
            code: call("index", "{ endpoint: 'contatos' + id }"),
            errors: [{ messageId: "endpoint" }]
        },
        {
            name: "endpoint template calls a function",
            filename,
            code: call("store", "{ endpoint: `nfce/${String(id)}/enviar`, body }"),
            errors: [{ messageId: "endpoint" }]
        },
        {
            name: "missing endpoint",
            filename,
            code: call("index", "{ params: { pagina: 1 } }"),
            errors: [{ messageId: "missing" }]
        },
        {
            name: "index does not take id",
            filename,
            code: call("index", "{ endpoint: 'contatos', id: String(params.idContato) }"),
            errors: [{ messageId: "unexpected" }]
        },
        {
            name: "show without id",
            filename,
            code: call("show", "{ endpoint: 'contatos' }"),
            errors: [{ messageId: "missing" }]
        },
        {
            name: "bare identifier is not an id",
            filename,
            code: call("show", "{ endpoint: 'contatos', id: idContato }"),
            errors: [{ messageId: "id" }]
        },
        {
            name: "numeric id",
            filename,
            code: call("destroy", "{ endpoint: 'contatos', id: 1 }"),
            errors: [{ messageId: "id" }]
        },
        {
            name: "show does not take body",
            filename,
            code: call("show", "{ endpoint: 'contatos', id: String(params.idContato), body }"),
            errors: [{ messageId: "unexpected" }]
        },
        {
            name: "store without body",
            filename,
            code: call("store", "{ endpoint: 'contatos' }"),
            errors: [{ messageId: "missing" }]
        },
        {
            name: "body must be an object, identifier, or member",
            filename,
            code: call("store", "{ endpoint: 'contatos', body: 1 }"),
            errors: [{ messageId: "body" }]
        },
        {
            name: "params must be an object literal",
            filename,
            code: call("index", "{ endpoint: 'contatos', params }"),
            errors: [{ messageId: "option" }]
        },
        {
            name: "header flag must be true",
            filename,
            code: call("index", "{ endpoint: 'contatos', shouldIncludeHeadersInResponse: false }"),
            errors: [{ messageId: "option" }]
        },
        {
            name: "spread options are not a call shape",
            filename,
            code: call("index", "{ ...options }"),
            errors: [{ messageId: "argument" }]
        },
        {
            name: "a non-object argument",
            filename,
            code: call("index", "options"),
            errors: [{ messageId: "argument" }]
        }
    ]
})
