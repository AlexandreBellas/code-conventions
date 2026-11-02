<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules\ResponseExtendsBase;

use AlexandreBellas\CodeConventions\PhpStan\TestSupport\LoadsFixtures;
use AlexandreBellas\CodeConventions\PhpStan\TestSupport\NeutralConfig;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<ResponseExtendsBaseRule>
 */
final class ResponseExtendsBaseRuleTest extends RuleTestCase
{
    use LoadsFixtures;

    protected function getRule(): Rule
    {
        return new ResponseExtendsBaseRule(NeutralConfig::make());
    }

    public function testResponseDataExtendsWrongBase(): void
    {
        $file = $this->fixture('response-base/src/Entities/Orders/Schema/Get/GetResponseData.php');
        $this->analyse([$file], [
            ['GetResponseData must extend ResponseData.', 5],
        ]);
    }

    public function testResponseExtendsRoot(): void
    {
        $file = $this->fixture('response-base/ok/src/Entities/Orders/Schema/Get/GetResponse.php');
        $this->analyse([$file], []);
    }
}
