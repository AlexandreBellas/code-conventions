<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules\RequireGetInstance;

use AlexandreBellas\CodeConventions\PhpStan\TestSupport\LoadsFixtures;
use AlexandreBellas\CodeConventions\PhpStan\TestSupport\NeutralConfig;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<RequireGetInstanceRule>
 */
final class RequireGetInstanceRuleTest extends RuleTestCase
{
    use LoadsFixtures;

    protected function getRule(): Rule
    {
        return new RequireGetInstanceRule(NeutralConfig::make());
    }

    public function testMissingGetInstance(): void
    {
        $file = $this->fixture('get-instance/src/Entities/Orders/__tests__/OrdersTest.spec.php');
        $this->analyse([$file], [
            ['An entity spec defines private function getInstance(ApiRepository $repository).', 5],
        ]);
    }

    public function testHasGetInstance(): void
    {
        $file = $this->fixture('get-instance/ok/src/Entities/Orders/__tests__/OrdersTest.spec.php');
        $this->analyse([$file], []);
    }
}
