<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\EntityTestExtendsTestCaseRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\WritesFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<EntityTestExtendsTestCaseRule>
 */
final class EntityTestExtendsTestCaseRuleTest extends RuleTestCase
{
    use WritesFixtures;

    protected function getRule(): Rule
    {
        return new EntityTestExtendsTestCaseRule();
    }

    public function testDoesNotExtendTestCase(): void
    {
        $file = $this->fixture('extends-case/src/Entities/Contatos/__tests__/ContatosTest.spec.php', "<?php\n\nnamespace Tests\\Unit\\AleBatistella\\BlingErpApi\\Entities\\Contatos;\n\nclass ContatosTest {}\n");
        $this->analyse([$file], [
            ['An entity spec extends PHPUnit\\Framework\\TestCase.', 5],
        ]);
    }

    public function testExtendsTestCase(): void
    {
        $file = $this->fixture('extends-case/ok/src/Entities/Contatos/__tests__/ContatosTest.spec.php', "<?php\n\nnamespace Tests\\Unit\\AleBatistella\\BlingErpApi\\Entities\\Contatos;\n\nclass ContatosTest extends TestCase {}\n");
        $this->analyse([$file], []);
    }
}
