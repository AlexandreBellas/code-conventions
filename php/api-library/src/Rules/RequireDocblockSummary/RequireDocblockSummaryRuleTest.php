<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules\RequireDocblockSummary;

use AlexandreBellas\CodeConventions\PhpStan\TestSupport\LoadsFixtures;
use AlexandreBellas\CodeConventions\PhpStan\TestSupport\NeutralConfig;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<RequireDocblockSummaryRule>
 */
final class RequireDocblockSummaryRuleTest extends RuleTestCase
{
    use LoadsFixtures;

    protected function getRule(): Rule
    {
        return new RequireDocblockSummaryRule(NeutralConfig::make());
    }

    public function testMissingSummary(): void
    {
        $file = $this->fixture('summary/src/Entities/Orders/Schema/Get/GetParams.php');
        $this->analyse([$file], [
            ['GetParams must have a one-line docblock summary.', 5],
        ]);
    }

    public function testHasSummary(): void
    {
        $file = $this->fixture('summary/ok/src/Entities/Orders/Schema/Get/GetParams.php');
        $this->analyse([$file], []);
    }
}
