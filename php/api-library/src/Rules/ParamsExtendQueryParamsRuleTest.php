<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\ParamsExtendQueryParamsRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\LoadsFixtures;
use AlexandreBellas\CodeConventions\PhpStan\Tests\NeutralConfig;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<ParamsExtendQueryParamsRule>
 */
final class ParamsExtendQueryParamsRuleTest extends RuleTestCase
{
    use LoadsFixtures;

    protected function getRule(): Rule
    {
        return new ParamsExtendQueryParamsRule(NeutralConfig::make());
    }

    public function testDoesNotExtendQuery(): void
    {
        $file = $this->fixture('params-extend/src/Entities/Orders/Schema/Get/GetParams.php');
        $this->analyse([$file], [
            ['GetParams must extend Query.', 5],
        ]);
    }

    public function testExtendsQuery(): void
    {
        $file = $this->fixture('params-extend/ok/src/Entities/Orders/Schema/Get/GetParams.php');
        $this->analyse([$file], []);
    }
}
