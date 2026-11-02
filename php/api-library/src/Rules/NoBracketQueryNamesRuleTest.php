<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\NoBracketQueryNamesRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\LoadsFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<NoBracketQueryNamesRule>
 */
final class NoBracketQueryNamesRuleTest extends RuleTestCase
{
    use LoadsFixtures;

    protected function getRule(): Rule
    {
        return new NoBracketQueryNamesRule();
    }

    public function testBracketKey(): void
    {
        $file = $this->fixture('brackets/bad.php');
        $this->analyse([$file], [
            ['Query name "idsOrders[]" already ends with []. Leave the PHP name without brackets.', 3],
        ]);
    }

    public function testPlainKey(): void
    {
        $file = $this->fixture('brackets/ok.php');
        $this->analyse([$file], []);
    }
}
