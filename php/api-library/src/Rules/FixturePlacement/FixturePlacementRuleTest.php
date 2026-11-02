<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules\FixturePlacement;

use AlexandreBellas\CodeConventions\PhpStan\TestSupport\LoadsFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<FixturePlacementRule>
 */
final class FixturePlacementRuleTest extends RuleTestCase
{
    use LoadsFixtures;

    protected function getRule(): Rule
    {
        return new FixturePlacementRule();
    }

    public function testJsonInSchema(): void
    {
        $this->fixture('fixtures/src/Entities/Orders/Schema/Get/response.json');
        $file = $this->fixture('fixtures/src/Entities/Orders/Orders.php');
        $this->analyse([$file], [
            ['JSON fixture Schema/Get/response.json does not belong in Schema/. Put it under __tests__/.', 3],
        ]);
    }

    public function testSpecOnly(): void
    {
        $this->fixture('fixtures-ok/src/Entities/Orders/__tests__/OrdersTest.spec.php');
        $file = $this->fixture('fixtures-ok/src/Entities/Orders/Orders.php');
        $this->analyse([$file], []);
    }
}
