<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\EntityRepositoryCallRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\WritesFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<EntityRepositoryCallRule>
 */
final class EntityRepositoryCallRuleTest extends RuleTestCase
{
    use WritesFixtures;

    protected function getRule(): Rule
    {
        return new EntityRepositoryCallRule();
    }

    public function testMissingRepositoryCall(): void
    {
        $file = $this->fixture('repository-call/src/Entities/Contatos/Contatos.php', <<<'CODE'
<?php

namespace AleBatistella\BlingErpApi\Entities\Contatos;

class Contatos extends BaseEntity
{
    public function execute(): void
    {
    }
}

CODE);
        $this->analyse([$file], [
            ['Public entity method execute must call exactly one of $this->repository->index, show, store, update, replace, or destroy.', 7],
            ['execute must return *Response::fromResponse(...), and the return type must be that response class or null.', 7],
        ]);
    }

    public function testRepositoryCall(): void
    {
        $file = $this->fixture('repository-call/ok/src/Entities/Contatos/Contatos.php', <<<'CODE'
<?php

namespace AleBatistella\BlingErpApi\Entities\Contatos;

class Contatos extends BaseEntity
{
    public function get(GetParams|array|null $params = null): GetResponse
    {
        $response = $this->repository->index(
            new RequestOptions(
                endpoint: "contatos",
                queryParams: $params
            )
        );

        return GetResponse::fromResponse($response);
    }
}

CODE);
        $this->analyse([$file], []);
    }
}
