<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules\RequireEntitySpec;

use AlexandreBellas\CodeConventions\PhpStan\TestSupport\LoadsFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<RequireEntitySpecRule>
 */
final class RequireEntitySpecRuleTest extends RuleTestCase
{
    use LoadsFixtures;

    protected function getRule(): Rule
    {
        return new RequireEntitySpecRule();
    }

    public function testMissingSpec(): void
    {
        $file = $this->fixture('spec/src/Entities/Orders/Orders.php');
        $this->analyse([$file], [
            ['Orders has no __tests__/OrdersTest.spec.php.', 3],
        ]);
    }

    public function testHasSpec(): void
    {
        $this->fixture('spec-ok/src/Entities/Orders/__tests__/OrdersTest.spec.php');
        $file = $this->fixture('spec-ok/src/Entities/Orders/Orders.php');
        $this->analyse([$file], []);
    }
}
