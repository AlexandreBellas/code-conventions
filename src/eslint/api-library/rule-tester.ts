import { RuleTester } from "@typescript-eslint/rule-tester"
import parser from "@typescript-eslint/parser"
import { afterAll, describe, it } from "vitest"

RuleTester.afterAll = afterAll
RuleTester.describe = describe
RuleTester.it = it

export function createRuleTester(): RuleTester {
    return new RuleTester({
        languageOptions: {
            parser,
            parserOptions: {
                ecmaVersion: "latest",
                sourceType: "module"
            }
        }
    })
}
