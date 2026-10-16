import { createRuleTester } from "./rule-tester"
import rule from "./enforce-type-export-style"

const ruleTester = createRuleTester()
const sharedFile = "/proj/src/entities/@shared/types/situacao.type.ts"
const entityFile = "/proj/src/entities/produtos/types/situacao.type.ts"
const interfaceFile = "/proj/src/entities/produtos/interfaces/get.interface.ts"
const sharedInterface = "/proj/src/entities/@shared/interfaces/error.interface.ts"

ruleTester.run("enforce-type-export-style", rule, {
    valid: [
        {
            name: "shared type is a default export",
            filename: sharedFile,
            code: "type ISituacao = 0 | 1\nexport default ISituacao"
        },
        {
            name: "entity type is a named export",
            filename: entityFile,
            code: "export type ISituacao = 1 | 2 | 3"
        },
        {
            name: "default import from shared types",
            filename: interfaceFile,
            code: "import ISituacao from '../../@shared/types/situacao.type'"
        },
        {
            name: "named import from the entity types folder",
            filename: interfaceFile,
            code: "import { ICriterio } from '../types/criterio.type'"
        },
        {
            name: "shared interfaces import shared types with a default import",
            filename: sharedInterface,
            code: "import IErrorType from '../types/error-type.type'"
        }
    ],
    invalid: [
        {
            name: "shared file uses export type",
            filename: sharedFile,
            code: "export type ISituacao = 0 | 1",
            errors: [{ messageId: "sharedTypeMustDefault" }]
        },
        {
            name: "shared file has no default export",
            filename: sharedFile,
            code: "type ISituacao = 0 | 1",
            errors: [{ messageId: "sharedTypeMustDefault" }]
        },
        {
            name: "entity file uses a default export",
            filename: entityFile,
            code: "type ISituacao = 1 | 2\nexport default ISituacao",
            errors: [{ messageId: "entityTypeMustBeNamed" }]
        },
        {
            name: "named import of a shared type",
            filename: interfaceFile,
            code: "import { ISituacao } from '../../@shared/types/situacao.type'",
            errors: [{ messageId: "sharedTypeDefaultImport", data: { name: "ISituacao" } }]
        },
        {
            name: "default import of an entity type",
            filename: interfaceFile,
            code: "import ICriterio from '../types/criterio.type'",
            errors: [{ messageId: "entityTypeNamedImport", data: { name: "ICriterio" } }]
        }
    ]
})
