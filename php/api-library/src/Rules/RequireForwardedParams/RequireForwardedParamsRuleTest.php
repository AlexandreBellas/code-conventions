<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules\RequireForwardedParams;

use AlexandreBellas\CodeConventions\PhpStan\TestSupport\LoadsFixtures;
use AlexandreBellas\CodeConventions\PhpStan\TestSupport\NeutralConfig;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<RequireForwardedParamsRule>
 */
final class RequireForwardedParamsRuleTest extends RuleTestCase
{
    use LoadsFixtures;

    protected function getRule(): Rule
    {
        return new RequireForwardedParamsRule(NeutralConfig::make());
    }

    public function testDropsParams(): void
    {
        $file = $this->fixture('forward/src/Entities/Orders/Orders.php');
        $this->analyse([$file], [
            ['$params must be forwarded as the queryParams argument of Request.', 7],
        ]);
    }

    public function testForwardsParams(): void
    {
        $file = $this->fixture('forward/ok/src/Entities/Orders/Orders.php');
        $this->analyse([$file], []);
    }
}
