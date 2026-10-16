import { createRuleTester } from "./rule-tester"
import rule from "./no-src-alias-imports"

const ruleTester = createRuleTester()
const filename = "/proj/src/entities/contatos/index.ts"

ruleTester.run("no-src-alias-imports", rule, {
    valid: [
        {
            name: "relative import",
            filename,
            code: "import { Entity } from '../@shared/entity'"
        },
        {
            name: "package import",
            filename,
            code: "import axios from 'axios'"
        },
        {
            name: "spec file",
            filename: "/proj/src/entities/contatos/__tests__/index.spec.ts",
            code: "import { Entity } from 'src/entities/@shared/entity'"
        }
    ],
    invalid: [
        {
            name: "src import",
            filename,
            code: "import { Entity } from 'src/entities/@shared/entity'",
            errors: [{ messageId: "srcAlias", data: { source: "src/entities/@shared/entity" } }]
        },
        {
            name: "src re-export",
            filename,
            code: "export { Entity } from 'src/entities/@shared/entity'",
            errors: [{ messageId: "srcAlias" }]
        },
        {
            name: "src export all",
            filename: "/proj/src/bling.ts",
            code: "export * from 'src/entities/contatos'",
            errors: [{ messageId: "srcAlias" }]
        }
    ]
})
