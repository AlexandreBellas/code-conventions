<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules\EntityTestUsesTrait;

use AlexandreBellas\CodeConventions\PhpStan\TestSupport\LoadsFixtures;
use AlexandreBellas\CodeConventions\PhpStan\TestSupport\NeutralConfig;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<EntityTestUsesTraitRule>
 */
final class EntityTestUsesTraitRuleTest extends RuleTestCase
{
    use LoadsFixtures;

    protected function getRule(): Rule
    {
        return new EntityTestUsesTraitRule(NeutralConfig::make());
    }

    public function testMissingTrait(): void
    {
        $file = $this->fixture('trait/src/Entities/Orders/__tests__/OrdersTest.spec.php');
        $this->analyse([$file], [
            ['An entity spec uses FakeResponse.', 5],
        ]);
    }

    public function testUsesTrait(): void
    {
        $file = $this->fixture('trait/ok/src/Entities/Orders/__tests__/OrdersTest.spec.php');
        $this->analyse([$file], []);
    }
}
