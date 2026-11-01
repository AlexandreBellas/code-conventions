<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\RequireRepositoryThrowsRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\WritesFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<RequireRepositoryThrowsRule>
 */
final class RequireRepositoryThrowsRuleTest extends RuleTestCase
{
    use WritesFixtures;

    protected function getRule(): Rule
    {
        return new RequireRepositoryThrowsRule();
    }

    public function testMissingThrows(): void
    {
        $file = $this->fixture('throws/src/Entities/Contatos/Contatos.php', <<<'CODE'
<?php

namespace AleBatistella\BlingErpApi\Entities\Contatos;

class Contatos extends BaseEntity
{
    public function get(): GetResponse
    {
        $response = $this->repository->index(new RequestOptions(endpoint: "contatos"));

        return GetResponse::fromResponse($response);
    }
}

CODE);
        $this->analyse([$file], [
            ['get must document `@throws BlingApiException|BlingInternalException`.', 7],
        ]);
    }

    public function testDocumentsThrows(): void
    {
        $file = $this->fixture('throws/ok/src/Entities/Contatos/Contatos.php', <<<'CODE'
<?php

namespace AleBatistella\BlingErpApi\Entities\Contatos;

class Contatos extends BaseEntity
{
    /**
     * @throws BlingApiException|BlingInternalException
     */
    public function get(): GetResponse
    {
        $response = $this->repository->index(new RequestOptions(endpoint: "contatos"));

        return GetResponse::fromResponse($response);
    }
}

CODE);
        $this->analyse([$file], []);
    }
}
