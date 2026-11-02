<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\FunctionExistsWrapperRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\LoadsFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<FunctionExistsWrapperRule>
 */
final class FunctionExistsWrapperRuleTest extends RuleTestCase
{
    use LoadsFixtures;

    protected function getRule(): Rule
    {
        return new FunctionExistsWrapperRule();
    }

    public function testBareFunction(): void
    {
        $file = $this->fixture('function-exists/src/Helpers/functions.php');
        $this->analyse([$file], [
            ['convertDateToString must be wrapped in function_exists().', 3],
        ]);
    }

    public function testWrappedFunction(): void
    {
        $file = $this->fixture('function-exists/ok/src/Helpers/functions.php');
        $this->analyse([$file], []);
    }
}
