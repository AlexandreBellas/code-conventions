<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\PhpDocParamTypesRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\LoadsFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<PhpDocParamTypesRule>
 */
final class PhpDocParamTypesRuleTest extends RuleTestCase
{
    use LoadsFixtures;

    protected function getRule(): Rule
    {
        return new PhpDocParamTypesRule();
    }

    public function testTypeMismatch(): void
    {
        $file = $this->fixture('param-types/bad.php');
        $this->analyse([$file], [
            ['@param $idsOrders of __construct must use the signature type ?array.', 8],
        ]);
    }

    public function testMatchingType(): void
    {
        $file = $this->fixture('param-types/ok.php');
        $this->analyse([$file], []);
    }
}
