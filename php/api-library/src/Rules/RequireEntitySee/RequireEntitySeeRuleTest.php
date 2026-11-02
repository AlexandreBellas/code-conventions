<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules\RequireEntitySee;

use AlexandreBellas\CodeConventions\PhpStan\TestSupport\LoadsFixtures;
use AlexandreBellas\CodeConventions\PhpStan\TestSupport\NeutralConfig;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<RequireEntitySeeRule>
 */
final class RequireEntitySeeRuleTest extends RuleTestCase
{
    use LoadsFixtures;

    protected function getRule(): Rule
    {
        return new RequireEntitySeeRule(NeutralConfig::make());
    }

    public function testMissingSee(): void
    {
        $file = $this->fixture('see/src/Entities/Orders/Orders.php');
        $this->analyse([$file], [
            ['Orders has no @see link to docs.example.com.', 5],
            ['get has no @see link to docs.example.com.', 7],
        ]);
    }

    public function testHasSee(): void
    {
        $file = $this->fixture('see/ok/src/Entities/Orders/Orders.php');
        $this->analyse([$file], []);
    }
}
