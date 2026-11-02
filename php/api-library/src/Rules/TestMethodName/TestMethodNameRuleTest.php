<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules\TestMethodName;

use AlexandreBellas\CodeConventions\PhpStan\TestSupport\LoadsFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<TestMethodNameRule>
 */
final class TestMethodNameRuleTest extends RuleTestCase
{
    use LoadsFixtures;

    protected function getRule(): Rule
    {
        return new TestMethodNameRule();
    }

    public function testWrongMethodName(): void
    {
        $file = $this->fixture('test-name/src/Entities/Orders/__tests__/OrdersTest.spec.php');
        $this->analyse([$file], [
            ['testGet must be named testShould<Action>Successfully.', 7],
        ]);
    }

    public function testSuccessfulName(): void
    {
        $file = $this->fixture('test-name/ok/src/Entities/Orders/__tests__/OrdersTest.spec.php');
        $this->analyse([$file], []);
    }
}
