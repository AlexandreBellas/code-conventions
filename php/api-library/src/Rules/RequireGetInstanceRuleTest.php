<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\RequireGetInstanceRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\LoadsFixtures;
use AlexandreBellas\CodeConventions\PhpStan\Tests\NeutralConfig;
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
