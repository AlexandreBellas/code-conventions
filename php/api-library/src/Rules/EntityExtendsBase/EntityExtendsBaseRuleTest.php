<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules\EntityExtendsBase;

use AlexandreBellas\CodeConventions\PhpStan\TestSupport\LoadsFixtures;
use AlexandreBellas\CodeConventions\PhpStan\TestSupport\NeutralConfig;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<EntityExtendsBaseRule>
 */
final class EntityExtendsBaseRuleTest extends RuleTestCase
{
    use LoadsFixtures;

    protected function getRule(): Rule
    {
        return new EntityExtendsBaseRule(NeutralConfig::make());
    }

    public function testReadonlyEntity(): void
    {
        $file = $this->fixture('entity-base/src/Entities/Orders/Orders.php');
        $this->analyse([$file], [
            ['Entity class Orders must be a concrete, non-readonly subclass of Entity.', 5],
        ]);
    }

    public function testExtendsEntity(): void
    {
        $file = $this->fixture('entity-base/ok/src/Entities/Orders/Orders.php');
        $this->analyse([$file], []);
    }
}
