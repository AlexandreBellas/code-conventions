<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\ParamsPrepareDateRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\LoadsFixtures;
use AlexandreBellas\CodeConventions\PhpStan\Tests\NeutralConfig;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<ParamsPrepareDateRule>
 */
final class ParamsPrepareDateRuleTest extends RuleTestCase
{
    use LoadsFixtures;

    protected function getRule(): Rule
    {
        return new ParamsPrepareDateRule(NeutralConfig::make());
    }

    public function testRawDate(): void
    {
        $file = $this->fixture('prepare-date/src/Entities/Orders/Schema/Get/GetParams.php');
        $this->analyse([$file], [
            ['$createdFrom must be passed through normalizeDate.', 7],
        ]);
    }

    public function testPreparedDate(): void
    {
        $file = $this->fixture('prepare-date/ok/src/Entities/Orders/Schema/Get/GetParams.php');
        $this->analyse([$file], []);
    }
}
