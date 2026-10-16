import { createRuleTester } from "./rule-tester"
import rule from "./no-http-in-entities"

const ruleTester = createRuleTester()
const filename = "/proj/src/entities/contatos/index.ts"

ruleTester.run("no-http-in-entities", rule, {
    valid: [
        {
            name: "repository call",
            filename,
            code: "class Contatos { async get() { return await this.repository.index({ endpoint: 'contatos' }) } }"
        },
        {
            name: "type-only axios import is erased",
            filename,
            code: "import type { AxiosInstance } from 'axios'"
        },
        {
            name: "oauth client may import axios",
            filename: "/proj/src/auth/oauth-client.ts",
            code: "import axios from 'axios'"
        },
        {
            name: "spec file",
            filename: "/proj/src/entities/contatos/__tests__/index.spec.ts",
            code: "import axios from 'axios'"
        }
    ],
    invalid: [
        {
            name: "axios import",
            filename,
            code: "import axios from 'axios'",
            errors: [{ messageId: "httpImport", data: { name: "axios" } }]
        },
        {
            name: "node:http import",
            filename,
            code: "import http from 'node:http'",
            errors: [{ messageId: "httpImport", data: { name: "node:http" } }]
        },
        {
            name: "dynamic axios import",
            filename,
            code: "const client = import('axios')",
            errors: [{ messageId: "httpImport", data: { name: "axios" } }]
        },
        {
            name: "fetch call",
            filename,
            code: "const response = fetch('https://api.bling.com.br')",
            errors: [{ messageId: "fetchCall" }]
        },
        {
            name: "globalThis.fetch call",
            filename,
            code: "const response = globalThis.fetch('https://api.bling.com.br')",
            errors: [{ messageId: "fetchCall" }]
        }
    ]
})
