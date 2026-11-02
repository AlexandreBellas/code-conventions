<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules\BackedEnum;

use AlexandreBellas\CodeConventions\PhpStan\TestSupport\LoadsFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<BackedEnumRule>
 */
final class BackedEnumRuleTest extends RuleTestCase
{
    use LoadsFixtures;

    protected function getRule(): Rule
    {
        return new BackedEnumRule();
    }

    public function testPureEnum(): void
    {
        $file = $this->fixture('backed/src/Entities/Orders/Enum/Status.php');
        $this->analyse([$file], [
            ['Status must be a backed enum (`enum Status: string` or `: int`).', 5],
        ]);
    }

    public function testStringBacked(): void
    {
        $file = $this->fixture('backed/ok/src/Entities/Orders/Enum/Status.php');
        $this->analyse([$file], []);
    }
}
