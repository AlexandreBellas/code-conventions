<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\RequireEntitySpecRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\WritesFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<RequireEntitySpecRule>
 */
final class RequireEntitySpecRuleTest extends RuleTestCase
{
    use WritesFixtures;

    protected function getRule(): Rule
    {
        return new RequireEntitySpecRule();
    }

    public function testMissingSpec(): void
    {
        $file = $this->fixture('spec/src/Entities/Contatos/Contatos.php', "<?php\n\nnamespace AleBatistella\\BlingErpApi\\Entities\\Contatos;\n\nclass Contatos extends BaseEntity {}\n");
        $this->analyse([$file], [
            ['Contatos has no __tests__/ContatosTest.spec.php.', 3],
        ]);
    }

    public function testHasSpec(): void
    {
        $this->fixture('spec-ok/src/Entities/Contatos/__tests__/ContatosTest.spec.php', "<?php\n");
        $file = $this->fixture('spec-ok/src/Entities/Contatos/Contatos.php', "<?php\n\nnamespace AleBatistella\\BlingErpApi\\Entities\\Contatos;\n\nclass Contatos extends BaseEntity {}\n");
        $this->analyse([$file], []);
    }
}
