<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\TestNamespaceRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\WritesFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<TestNamespaceRule>
 */
final class TestNamespaceRuleTest extends RuleTestCase
{
    use WritesFixtures;

    protected function getRule(): Rule
    {
        return new TestNamespaceRule();
    }

    public function testWrongTestNamespace(): void
    {
        $file = $this->fixture('test-namespace/src/Entities/Contatos/__tests__/ContatosTest.spec.php', "<?php\n\nnamespace Wrong;\n\nclass ContatosTest {}\n");
        $this->analyse([$file], [
            ['Test namespace must be Tests\\Unit\\AleBatistella\\BlingErpApi\\Entities\\Contatos.', 3],
        ]);
    }

    public function testFacadeSpecNamespace(): void
    {
        $file = $this->fixture('test-namespace/ok/src/BlingTest.spec.php', "<?php\n\nnamespace Tests\\Unit\\AleBatistella\\BlingErpApi;\n\nclass BlingTest {}\n");
        $this->analyse([$file], []);
    }
}
