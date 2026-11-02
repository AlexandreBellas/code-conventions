<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\EntityMethodDocOrderRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\LoadsFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<EntityMethodDocOrderRule>
 */
final class EntityMethodDocOrderRuleTest extends RuleTestCase
{
    use LoadsFixtures;

    protected function getRule(): Rule
    {
        return new EntityMethodDocOrderRule();
    }

    public function testSeeBeforeReturn(): void
    {
        $file = $this->fixture('doc-order/src/Entities/Orders/Orders.php');
        $this->analyse([$file], [
            ['Public entity method docblocks list the description, then @param, @return, @throws, and @see, in that order.', 13],
        ]);
    }

    public function testOrderedTags(): void
    {
        $file = $this->fixture('doc-order/ok/src/Entities/Orders/Orders.php');
        $this->analyse([$file], []);
    }
}
