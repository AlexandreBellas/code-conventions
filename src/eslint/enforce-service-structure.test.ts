import { RuleTester } from "@typescript-eslint/rule-tester"
import { afterAll, describe, it } from "vitest"
import rule from "./enforce-service-structure"

RuleTester.afterAll = afterAll
RuleTester.describe = describe
RuleTester.it = it

const ruleTester = new RuleTester()

const code = "export const value = 1"

ruleTester.run("enforce-service-structure", rule, {
    valid: [
        {
            name: "allows the gateway interface at the service root",
            code: "export interface IProjectsPrivateGateway { list(): Promise<void> }",
            filename: "src/services/private/projects/IProjectsPrivateGateway.ts"
        },
        {
            name: "allows the api implementation variant",
            code: "export class ProjectsApi {}",
            filename: "src/services/private/projects/api/projects.api.ts"
        },
        {
            name: "allows the in-memory implementation variant",
            code,
            filename: "src/services/private/projects/in-memory/projects.in-memory.ts"
        },
        {
            name: "allows the websocket implementation variant",
            code,
            filename: "src/services/public/interview-engines/websocket/index.ts"
        },
        {
            name: "allows vendor-named implementation variants",
            code,
            filename: "src/services/recording/livekit/index.ts"
        },
        {
            name: "allows the @shared container, which is not a component folder",
            code,
            filename: "src/services/@shared/api/BaseApiService.ts"
        },
        {
            name: "allows a file directly under src/services",
            code,
            filename: "src/services/index.ts"
        },
        // Near-miss folder names must not fire
        {
            name: "allows folders whose name only resembles utils",
            code,
            filename: "src/services/private/artefacts/utilities/parse.ts"
        },
        {
            name: "allows folders whose name only resembles constants",
            code,
            filename: "src/services/private/artefacts/constant/stream.ts"
        },
        {
            name: "allows folders whose name only resembles @types",
            code,
            filename: "src/services/private/projects/types/project.ts"
        },
        // A `services` segment not directly under `src` is out of scope
        {
            name: "ignores service hooks living under src/hooks/services",
            code,
            filename: "src/hooks/services/utils/buildHeaders.ts"
        },
        {
            name: "ignores a services folder nested inside a page",
            code,
            filename: "src/pages/Analyse/services/utils/buildFilters.ts"
        },
        // Component folders keep working everywhere else
        {
            name: "ignores component utils folders",
            code,
            filename: "src/components/GlautButton/utils/resolveVariant.ts"
        },
        // Exempt file kinds inside a banned folder
        {
            name: "exempts test files inside a component folder",
            code,
            filename: "src/services/private/artefacts/utils/parseArtefactStreamMessage.test.ts"
        },
        {
            name: "exempts spec files inside a component folder",
            code,
            filename: "src/services/private/artefacts/utils/parseArtefactStreamMessage.spec.ts"
        },
        {
            name: "exempts stories files inside a component folder",
            code,
            filename: "src/services/private/artefacts/utils/parseArtefactStreamMessage.stories.tsx"
        },
        {
            name: "allows implementation variants written with backslash separators",
            code,
            filename: "src\\services\\private\\projects\\api\\projects.api.ts"
        }
    ],
    invalid: [
        {
            name: "reports a utils folder inside a service",
            code,
            filename: "src/services/private/artefacts/utils/parseArtefactStreamMessage.ts",
            errors: [{ messageId: "componentFolderInService", data: { folder: "utils" } }]
        },
        {
            name: "reports a constants folder inside an implementation variant",
            code,
            filename: "src/services/@shared/api/constants/stream.ts",
            errors: [{ messageId: "componentFolderInService", data: { folder: "constants" } }]
        },
        {
            name: "reports an @types folder inside a service",
            code: "export type IProject = { id: string }",
            filename: "src/services/private/projects/@types/project.ts",
            errors: [{ messageId: "componentFolderInService", data: { folder: "@types" } }]
        },
        {
            name: "reports a hooks folder inside a service",
            code,
            filename: "src/services/private/projects/hooks/useProjects.ts",
            errors: [{ messageId: "componentFolderInService", data: { folder: "hooks" } }]
        },
        {
            name: "reports a components folder inside a service",
            code,
            filename: "src/services/private/projects/components/Row/index.ts",
            errors: [{ messageId: "componentFolderInService", data: { folder: "components" } }]
        },
        {
            name: "reports a contexts folder inside a service",
            code,
            filename: "src/services/private/projects/contexts/ProjectsProvider.ts",
            errors: [{ messageId: "componentFolderInService", data: { folder: "contexts" } }]
        },
        {
            name: "reports a docs folder inside a service",
            code,
            filename: "src/services/private/projects/docs/usage.ts",
            errors: [{ messageId: "componentFolderInService", data: { folder: "docs" } }]
        },
        {
            name: "reports a stores folder inside a service",
            code,
            filename: "src/services/private/projects/stores/projectsStore.ts",
            errors: [{ messageId: "componentFolderInService", data: { folder: "stores" } }]
        },
        {
            name: "reports a component folder placed directly under src/services",
            code,
            filename: "src/services/utils/buildHeaders.ts",
            errors: [{ messageId: "componentFolderInService", data: { folder: "utils" } }]
        },
        {
            name: "reports the outermost folder once when component folders are stacked",
            code,
            filename: "src/services/private/artefacts/utils/constants/stream.ts",
            errors: [{ messageId: "componentFolderInService", data: { folder: "utils" } }]
        },
        {
            name: "reports a component folder in an absolute path",
            code,
            filename: "/home/dev/glaut-app/src/services/private/artefacts/utils/parse.ts",
            errors: [{ messageId: "componentFolderInService", data: { folder: "utils" } }]
        },
        {
            name: "reports a component folder written with backslash separators",
            code,
            filename: "src\\services\\private\\artefacts\\utils\\parse.ts",
            errors: [{ messageId: "componentFolderInService", data: { folder: "utils" } }]
        }
    ]
})
