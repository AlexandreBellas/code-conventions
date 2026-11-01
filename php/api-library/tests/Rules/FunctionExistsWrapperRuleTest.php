<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\FunctionExistsWrapperRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\WritesFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<FunctionExistsWrapperRule>
 */
final class FunctionExistsWrapperRuleTest extends RuleTestCase
{
    use WritesFixtures;

    protected function getRule(): Rule
    {
        return new FunctionExistsWrapperRule();
    }

    public function testBareFunction(): void
    {
        $file = $this->fixture('function-exists/src/Helpers/functions.php', "<?php\n\nfunction convertDateToString(): string { return \"\"; }\n");
        $this->analyse([$file], [
            ['convertDateToString must be wrapped in function_exists().', 3],
        ]);
    }

    public function testWrappedFunction(): void
    {
        $file = $this->fixture('function-exists/ok/src/Helpers/functions.php', "<?php\n\nif (!function_exists('convertDateToString')) {\n    function convertDateToString(): string { return \"\"; }\n}\n");
        $this->analyse([$file], []);
    }
}
