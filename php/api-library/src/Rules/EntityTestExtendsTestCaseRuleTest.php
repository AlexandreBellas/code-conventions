<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\EntityTestExtendsTestCaseRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\LoadsFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<EntityTestExtendsTestCaseRule>
 */
final class EntityTestExtendsTestCaseRuleTest extends RuleTestCase
{
    use LoadsFixtures;

    protected function getRule(): Rule
    {
        return new EntityTestExtendsTestCaseRule();
    }

    public function testDoesNotExtendTestCase(): void
    {
        $file = $this->fixture('extends-case/src/Entities/Orders/__tests__/OrdersTest.spec.php');
        $this->analyse([$file], [
            ['An entity spec extends PHPUnit\\Framework\\TestCase.', 5],
        ]);
    }

    public function testExtendsTestCase(): void
    {
        $file = $this->fixture('extends-case/ok/src/Entities/Orders/__tests__/OrdersTest.spec.php');
        $this->analyse([$file], []);
    }
}
