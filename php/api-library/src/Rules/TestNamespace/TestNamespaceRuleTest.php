<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules\TestNamespace;

use AlexandreBellas\CodeConventions\PhpStan\TestSupport\LoadsFixtures;
use AlexandreBellas\CodeConventions\PhpStan\TestSupport\NeutralConfig;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<TestNamespaceRule>
 */
final class TestNamespaceRuleTest extends RuleTestCase
{
    use LoadsFixtures;

    protected function getRule(): Rule
    {
        return new TestNamespaceRule(NeutralConfig::make());
    }

    public function testWrongTestNamespace(): void
    {
        $file = $this->fixture('test-namespace/src/Entities/Orders/__tests__/OrdersTest.spec.php');
        $this->analyse([$file], [
            ['Test namespace must be Tests\\Unit\\Acme\\ApiClient\\Entities\\Orders.', 3],
        ]);
    }

    public function testFacadeSpecNamespace(): void
    {
        $file = $this->fixture('test-namespace/ok/src/ClientTest.spec.php');
        $this->analyse([$file], []);
    }
}
