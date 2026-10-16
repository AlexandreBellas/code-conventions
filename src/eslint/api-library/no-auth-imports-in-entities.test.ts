import { createRuleTester } from "./rule-tester"
import rule from "./no-auth-imports-in-entities"

const ruleTester = createRuleTester()
const filename = "/proj/src/entities/contatos/index.ts"

ruleTester.run("no-auth-imports-in-entities", rule, {
    valid: [
        {
            name: "repository import",
            filename,
            code: "import { IBlingRepository } from '../../repositories/bling.repository'"
        },
        {
            name: "auth module may import itself",
            filename: "/proj/src/auth/oauth-client.ts",
            code: "import { createAuthProvider } from './create-auth-provider'"
        }
    ],
    invalid: [
        {
            name: "relative import of src/auth",
            filename,
            code: "import { OAuthClient } from '../../auth/oauth-client'",
            errors: [{ messageId: "authImport" }]
        },
        {
            name: "src/auth specifier",
            filename,
            code: "import { OAuthClient } from 'src/auth/oauth-client'",
            errors: [{ messageId: "authImport" }]
        }
    ]
})
