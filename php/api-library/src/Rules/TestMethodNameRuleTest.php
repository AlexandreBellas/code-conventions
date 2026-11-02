<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\TestMethodNameRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\LoadsFixtures;
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
