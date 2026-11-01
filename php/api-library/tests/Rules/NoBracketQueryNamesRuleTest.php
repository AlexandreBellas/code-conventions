<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\NoBracketQueryNamesRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\WritesFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<NoBracketQueryNamesRule>
 */
final class NoBracketQueryNamesRuleTest extends RuleTestCase
{
    use WritesFixtures;

    protected function getRule(): Rule
    {
        return new NoBracketQueryNamesRule();
    }

    public function testBracketKey(): void
    {
        $file = $this->fixture('brackets/bad.php', "<?php\n\n\$query = [\"idsContatos[]\" => [1]];\n");
        $this->analyse([$file], [
            ['Query name "idsContatos[]" already ends with []. Leave the PHP name without brackets.', 3],
        ]);
    }

    public function testPlainKey(): void
    {
        $file = $this->fixture('brackets/ok.php', "<?php\n\n\$query = [\"idsContatos\" => [1]];\n");
        $this->analyse([$file], []);
    }
}
