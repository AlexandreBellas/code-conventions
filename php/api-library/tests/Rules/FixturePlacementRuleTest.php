<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\FixturePlacementRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\WritesFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<FixturePlacementRule>
 */
final class FixturePlacementRuleTest extends RuleTestCase
{
    use WritesFixtures;

    protected function getRule(): Rule
    {
        return new FixturePlacementRule();
    }

    public function testJsonInSchema(): void
    {
        $this->fixture('fixtures/src/Entities/Contatos/Schema/Get/response.json', "{}\n");
        $file = $this->fixture('fixtures/src/Entities/Contatos/Contatos.php', "<?php\n\nnamespace AleBatistella\\BlingErpApi\\Entities\\Contatos;\n\nclass Contatos extends BaseEntity {}\n");
        $this->analyse([$file], [
            ['JSON fixture Schema/Get/response.json does not belong in Schema/. Put it under __tests__/.', 3],
        ]);
    }

    public function testSpecOnly(): void
    {
        $this->fixture('fixtures-ok/src/Entities/Contatos/__tests__/ContatosTest.spec.php', "<?php\n");
        $file = $this->fixture('fixtures-ok/src/Entities/Contatos/Contatos.php', "<?php\n\nnamespace AleBatistella\\BlingErpApi\\Entities\\Contatos;\n\nclass Contatos extends BaseEntity {}\n");
        $this->analyse([$file], []);
    }
}
