<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\NoStrictTypesRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\WritesFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<NoStrictTypesRule>
 */
final class NoStrictTypesRuleTest extends RuleTestCase
{
    use WritesFixtures;

    protected function getRule(): Rule
    {
        return new NoStrictTypesRule();
    }

    public function testDeclaresStrictTypes(): void
    {
        $file = $this->fixture('no-strict-types/bad.php', "<?php\n\ndeclare(strict_types=1);\n");
        $this->analyse([$file], [
            ['Do not declare strict_types. Files start with <?php and a namespace and leave strict_types unset.', 3],
        ]);
    }

    public function testWithoutStrictTypes(): void
    {
        $file = $this->fixture('no-strict-types/ok.php', "<?php\n\nnamespace Example;\n");
        $this->analyse([$file], []);
    }
}
