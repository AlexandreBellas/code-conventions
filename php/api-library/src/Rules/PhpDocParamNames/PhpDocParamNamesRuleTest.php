<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules\PhpDocParamNames;

use AlexandreBellas\CodeConventions\PhpStan\TestSupport\LoadsFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<PhpDocParamNamesRule>
 */
final class PhpDocParamNamesRuleTest extends RuleTestCase
{
    use LoadsFixtures;

    protected function getRule(): Rule
    {
        return new PhpDocParamNamesRule();
    }

    public function testWrongParamName(): void
    {
        $file = $this->fixture('param-names/bad.php');
        $this->analyse([$file], [
            ['@param $other does not match a parameter of find.', 8],
            ['Parameter $id of find is missing an @param tag with that name.', 8],
        ]);
    }

    public function testMatchingParamName(): void
    {
        $file = $this->fixture('param-names/ok.php');
        $this->analyse([$file], []);
    }
}
