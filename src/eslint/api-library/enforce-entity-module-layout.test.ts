import { createRuleTester } from "./rule-tester"
import rule from "./enforce-entity-module-layout"

const ruleTester = createRuleTester()

ruleTester.run("enforce-entity-module-layout", rule, {
    valid: [
        {
            name: "class only index",
            filename: "/proj/src/entities/contatos/index.ts",
            code: "export class Contatos extends Entity {}"
        },
        {
            name: "arrow function inside a class method",
            filename: "/proj/src/entities/contatos/index.ts",
            code: "export class Contatos extends Entity { async get() { const value = () => 1\n return value() } }"
        },
        {
            name: "interface file exports IGetResponse and other types",
            filename: "/proj/src/entities/contatos/interfaces/get.interface.ts",
            code: "export interface IGetParams { pagina?: number }\nexport interface IGetResponse { data: unknown[] }\nexport type IAlias = string"
        },
        {
            name: "kebab interface file exports IAnyNameHereResponse",
            filename: "/proj/src/entities/contatos/interfaces/any-name-here.interface.ts",
            code: "export interface IAnyNameHereParams { id: number }\nexport interface IAnyNameHereResponse { id: number }"
        },
        {
            name: "re-exported interface still counts",
            filename: "/proj/src/entities/contatos/interfaces/get.interface.ts",
            code: "interface IGetResponse { id: number }\nexport { IGetResponse }"
        },
        {
            name: "void response may be a type alias",
            filename: "/proj/src/entities/contatos/interfaces/delete.interface.ts",
            code: "export interface IDeleteParams { id: number }\nexport type IDeleteResponse = void"
        },
        {
            name: "type file",
            filename: "/proj/src/entities/contatos/types/situacao.type.ts",
            code: "export type ISituacao = 1 | 2"
        },
        {
            name: "kebab type file",
            filename: "/proj/src/entities/contatos/types/tipo-producao.type.ts",
            code: "export type ITipoProducao = 'P' | 'T'"
        },
        {
            name: "shared entity runtime is exempt",
            filename: "/proj/src/entities/@shared/entity.ts",
            code: "export abstract class Entity { protected prepare() { return 1 } }"
        },
        {
            name: "shared type file is exempt from the required name",
            filename: "/proj/src/entities/@shared/types/situacao.type.ts",
            code: "export type Situacao = 0 | 1"
        }
    ],
    invalid: [
        {
            name: "helper function in index",
            filename: "/proj/src/entities/contatos/index.ts",
            code: "function helper() { return 1 }\nexport class Contatos extends Entity {}",
            errors: [{ messageId: "helperInIndex" }]
        },
        {
            name: "module-level arrow beside the class",
            filename: "/proj/src/entities/contatos/index.ts",
            code: "const helper = () => 1\nexport class Contatos extends Entity {}",
            errors: [{ messageId: "helperInIndex" }]
        },
        {
            name: "interface in index",
            filename: "/proj/src/entities/contatos/index.ts",
            code: "export interface IGetParams { pagina?: number }\nexport class Contatos extends Entity {}",
            errors: [{ messageId: "typeOrInterfaceInIndex" }]
        },
        {
            name: "type alias in index",
            filename: "/proj/src/entities/contatos/index.ts",
            code: "export type ISituacao = 0 | 1\nexport class Contatos extends Entity {}",
            errors: [{ messageId: "typeOrInterfaceInIndex" }]
        },
        {
            name: "function in interfaces",
            filename: "/proj/src/entities/contatos/interfaces/get.interface.ts",
            code: "export interface IGetResponse { id: number }\nexport function helper() { return 1 }",
            errors: [{ messageId: "runtimeInTypeFiles" }]
        },
        {
            name: "class in types",
            filename: "/proj/src/entities/contatos/types/situacao.type.ts",
            code: "export type ISituacao = 0 | 1\nexport class Situacao {}",
            errors: [{ messageId: "runtimeInTypeFiles" }]
        },
        {
            name: "interface file without the suffix",
            filename: "/proj/src/entities/contatos/interfaces/get.ts",
            code: "export interface IGetParams { pagina?: number }",
            errors: [{ messageId: "wrongTypeFolderSuffix", data: { name: "get.ts" } }]
        },
        {
            name: "type file without the suffix",
            filename: "/proj/src/entities/contatos/types/situacao.ts",
            code: "export type ISituacao = 0 | 1",
            errors: [{ messageId: "wrongTypeFolderSuffix", data: { name: "situacao.ts" } }]
        },
        {
            name: "get.interface.ts without IGetResponse",
            filename: "/proj/src/entities/contatos/interfaces/get.interface.ts",
            code: "export interface IGetParams { pagina?: number }",
            errors: [{ messageId: "missingRequiredInterface", data: { file: "get.interface.ts", name: "IGetResponse" } }]
        },
        {
            name: "kebab interface file without IAnyNameHereResponse",
            filename: "/proj/src/entities/contatos/interfaces/any-name-here.interface.ts",
            code: "export interface IAnyNameHereParams { id: number }",
            errors: [
                {
                    messageId: "missingRequiredInterface",
                    data: { file: "any-name-here.interface.ts", name: "IAnyNameHereResponse" }
                }
            ]
        },
        {
            name: "unexported IGetResponse does not count",
            filename: "/proj/src/entities/contatos/interfaces/get.interface.ts",
            code: "interface IGetResponse { id: number }",
            errors: [{ messageId: "missingRequiredInterface", data: { file: "get.interface.ts", name: "IGetResponse" } }]
        },
        {
            name: "situacao.type.ts without type ISituacao",
            filename: "/proj/src/entities/contatos/types/situacao.type.ts",
            code: "export type IOutra = 0 | 1",
            errors: [{ messageId: "missingRequiredType", data: { file: "situacao.type.ts", name: "ISituacao" } }]
        },
        {
            name: "interface ISituacao does not replace type ISituacao",
            filename: "/proj/src/entities/contatos/types/situacao.type.ts",
            code: "export interface ISituacao { id: number }",
            errors: [{ messageId: "missingRequiredType", data: { file: "situacao.type.ts", name: "ISituacao" } }]
        }
    ]
})
