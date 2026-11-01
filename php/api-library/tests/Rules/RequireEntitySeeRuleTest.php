<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\RequireEntitySeeRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\WritesFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<RequireEntitySeeRule>
 */
final class RequireEntitySeeRuleTest extends RuleTestCase
{
    use WritesFixtures;

    protected function getRule(): Rule
    {
        return new RequireEntitySeeRule();
    }

    public function testMissingSee(): void
    {
        $file = $this->fixture('see/src/Entities/Contatos/Contatos.php', "<?php\n\nnamespace AleBatistella\\BlingErpApi\\Entities\\Contatos;\n\nclass Contatos extends BaseEntity\n{\n    public function get(): GetResponse\n    {\n        return GetResponse::fromResponse(\$response);\n    }\n}\n");
        $this->analyse([$file], [
            ['Contatos has no @see link to developer.bling.com.br.', 5],
            ['get has no @see link to developer.bling.com.br.', 7],
        ]);
    }

    public function testHasSee(): void
    {
        $file = $this->fixture('see/ok/src/Entities/Contatos/Contatos.php', <<<'CODE'
<?php

namespace AleBatistella\BlingErpApi\Entities\Contatos;

/**
 * Entidade para interação com contatos.
 *
 * @see https://developer.bling.com.br/referencia#/Contatos
 */
class Contatos extends BaseEntity
{
    /**
     * Obtém contatos.
     *
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
