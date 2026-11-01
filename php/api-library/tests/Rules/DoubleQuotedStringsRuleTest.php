<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\DoubleQuotedStringsRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\WritesFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<DoubleQuotedStringsRule>
 */
final class DoubleQuotedStringsRuleTest extends RuleTestCase
{
    use WritesFixtures;

    protected function getRule(): Rule
    {
        return new DoubleQuotedStringsRule();
    }

    public function testSingleQuotedString(): void
    {
        $file = $this->fixture('quotes/bad.php', "<?php\n\n\$value = 'bad';\n");
        $this->analyse([$file], [
            ['PHP strings use double quotes.', 3],
        ]);
    }

    public function testDoubleQuotedString(): void
    {
        $file = $this->fixture('quotes/ok.php', "<?php\n\n\$value = \"ok\";\n");
        $this->analyse([$file], []);
    }
}
