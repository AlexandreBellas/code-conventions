<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\RequireForwardedParamsRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\WritesFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<RequireForwardedParamsRule>
 */
final class RequireForwardedParamsRuleTest extends RuleTestCase
{
    use WritesFixtures;

    protected function getRule(): Rule
    {
        return new RequireForwardedParamsRule();
    }

    public function testDropsParams(): void
    {
        $file = $this->fixture('forward/src/Entities/Contatos/Contatos.php', <<<'CODE'
<?php

namespace AleBatistella\BlingErpApi\Entities\Contatos;

class Contatos extends BaseEntity
{
    public function get(GetParams|array|null $params = null): GetResponse
    {
        $response = $this->repository->index(new RequestOptions(endpoint: "contatos"));

        return GetResponse::fromResponse($response);
    }
}

CODE);
        $this->analyse([$file], [
            ['$params must be forwarded as the queryParams argument of RequestOptions.', 7],
        ]);
    }

    public function testForwardsParams(): void
    {
        $file = $this->fixture('forward/ok/src/Entities/Contatos/Contatos.php', <<<'CODE'
<?php

namespace AleBatistella\BlingErpApi\Entities\Contatos;

class Contatos extends BaseEntity
{
    public function get(GetParams|array|null $params = null): GetResponse
    {
        $response = $this->repository->index(
            new RequestOptions(endpoint: "contatos", queryParams: $params)
        );

        return GetResponse::fromResponse($response);
    }
}

CODE);
        $this->analyse([$file], []);
    }
}
