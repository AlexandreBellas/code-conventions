<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\EntityMethodDocOrderRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\WritesFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<EntityMethodDocOrderRule>
 */
final class EntityMethodDocOrderRuleTest extends RuleTestCase
{
    use WritesFixtures;

    protected function getRule(): Rule
    {
        return new EntityMethodDocOrderRule();
    }

    public function testSeeBeforeReturn(): void
    {
        $file = $this->fixture('doc-order/src/Entities/Contatos/Contatos.php', <<<'CODE'
<?php

namespace AleBatistella\BlingErpApi\Entities\Contatos;

class Contatos extends BaseEntity
{
    /**
     * Obtém contatos.
     *
     * @see https://developer.bling.com.br/referencia#/Contatos/get_contatos
     * @return GetResponse
     */
    public function get(): GetResponse
    {
        return GetResponse::fromResponse($response);
    }
}

CODE);
        $this->analyse([$file], [
            ['Public entity method docblocks list the description, then @param, @return, @throws, and @see, in that order.', 13],
        ]);
    }

    public function testOrderedTags(): void
    {
        $file = $this->fixture('doc-order/ok/src/Entities/Contatos/Contatos.php', <<<'CODE'
<?php

namespace AleBatistella\BlingErpApi\Entities\Contatos;

class Contatos extends BaseEntity
{
    /**
     * Obtém contatos.
     *
     * @return GetResponse
     * @throws BlingApiException|BlingInternalException
     * @see https://developer.bling.com.br/referencia#/Contatos/get_contatos
     */
    public function get(): GetResponse
    {
        return GetResponse::fromResponse($response);
    }
}

CODE);
        $this->analyse([$file], []);
    }
}
