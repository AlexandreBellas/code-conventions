import { createRuleTester } from "./rule-tester"
import rule from "./no-cross-entity-imports"

const ruleTester = createRuleTester()
const filename = "/proj/src/entities/nfses/index.ts"

ruleTester.run("no-cross-entity-imports", rule, {
    valid: [
        {
            name: "local interface",
            filename,
            code: "import { ISendResponse } from './interfaces/send.interface'"
        },
        {
            name: "shared entity base",
            filename,
            code: "import { Entity } from '../@shared/entity'"
        },
        {
            name: "package import whose name contains entities",
            filename,
            code: "import helper from 'entities/helper'"
        },
        {
            name: "relative path that stays in this module",
            filename,
            code: "import { helper } from './not/entities/helper'"
        }
    ],
    invalid: [
        {
            name: "relative import of another entity",
            filename,
            code: "import { ISendResponse } from '../nfces/interfaces/send.interface'",
            errors: [{ messageId: "crossEntityImport", data: { module: "nfces" } }]
        },
        {
            name: "re-export of another entity",
            filename,
            code: "export { ISendResponse } from '../nfces/interfaces/send.interface'",
            errors: [{ messageId: "crossEntityImport", data: { module: "nfces" } }]
        }
    ]
})
