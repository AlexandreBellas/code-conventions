<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\EndpointKebabCaseRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\WritesFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<EndpointKebabCaseRule>
 */
final class EndpointKebabCaseRuleTest extends RuleTestCase
{
    use WritesFixtures;

    protected function getRule(): Rule
    {
        return new EndpointKebabCaseRule();
    }

    public function testCamelEndpoint(): void
    {
        $file = $this->fixture('endpoint/src/Entities/Contatos/Contatos.php', <<<'CODE'
<?php

namespace AleBatistella\BlingErpApi\Entities\Contatos;

class Contatos extends BaseEntity
{
    public function get(): GetResponse
    {
        return new RequestOptions(endpoint: "contatosTipos");
    }
}

CODE);
        $this->analyse([$file], [
            ['Endpoint segment "contatosTipos" must be kebab-case.', 9],
        ]);
    }

    public function testKebabEndpoint(): void
    {
        $file = $this->fixture('endpoint/ok/src/Entities/Contatos/Contatos.php', <<<'CODE'
<?php

namespace AleBatistella\BlingErpApi\Entities\Contatos;

class Contatos extends BaseEntity
{
    public function find(int $idContato): FindResponse
    {
        return new RequestOptions(endpoint: "contatos/$idContato/tipos");
    }
}

CODE);
        $this->analyse([$file], []);
    }
}
