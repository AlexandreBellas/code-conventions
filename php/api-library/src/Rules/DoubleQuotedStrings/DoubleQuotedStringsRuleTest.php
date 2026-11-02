<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules\DoubleQuotedStrings;

use AlexandreBellas\CodeConventions\PhpStan\TestSupport\LoadsFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<DoubleQuotedStringsRule>
 */
final class DoubleQuotedStringsRuleTest extends RuleTestCase
{
    use LoadsFixtures;

    protected function getRule(): Rule
    {
        return new DoubleQuotedStringsRule();
    }

    public function testSingleQuotedString(): void
    {
        $file = $this->fixture('quotes/bad.php');
        $this->analyse([$file], [
            ['PHP strings use double quotes.', 3],
        ]);
    }

    public function testDoubleQuotedString(): void
    {
        $file = $this->fixture('quotes/ok.php');
        $this->analyse([$file], []);
    }
}
