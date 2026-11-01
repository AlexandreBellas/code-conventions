<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\EntityTestUsesTraitRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\WritesFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<EntityTestUsesTraitRule>
 */
final class EntityTestUsesTraitRuleTest extends RuleTestCase
{
    use WritesFixtures;

    protected function getRule(): Rule
    {
        return new EntityTestUsesTraitRule();
    }

    public function testMissingTrait(): void
    {
        $file = $this->fixture('trait/src/Entities/Contatos/__tests__/ContatosTest.spec.php', "<?php\n\nnamespace Tests\\Unit\\AleBatistella\\BlingErpApi\\Entities\\Contatos;\n\nclass ContatosTest extends TestCase {}\n");
        $this->analyse([$file], [
            ['An entity spec uses TestResponseTrait.', 5],
        ]);
    }

    public function testUsesTrait(): void
    {
        $file = $this->fixture('trait/ok/src/Entities/Contatos/__tests__/ContatosTest.spec.php', "<?php\n\nnamespace Tests\\Unit\\AleBatistella\\BlingErpApi\\Entities\\Contatos;\n\nclass ContatosTest extends TestCase { use TestResponseTrait; }\n");
        $this->analyse([$file], []);
    }
}
