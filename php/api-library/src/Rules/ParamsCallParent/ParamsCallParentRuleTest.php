<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules\ParamsCallParent;

use AlexandreBellas\CodeConventions\PhpStan\TestSupport\LoadsFixtures;
use AlexandreBellas\CodeConventions\PhpStan\TestSupport\NeutralConfig;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<ParamsCallParentRule>
 */
final class ParamsCallParentRuleTest extends RuleTestCase
{
    use LoadsFixtures;

    protected function getRule(): Rule
    {
        return new ParamsCallParentRule(NeutralConfig::make());
    }

    public function testMissingParentCall(): void
    {
        $file = $this->fixture('parent-call/src/Entities/Orders/Schema/Get/GetParams.php');
        $this->analyse([$file], [
            ['GetParams::__construct must finish with parent::__construct(toArray($this)).', 7],
        ]);
    }

    public function testCallsParent(): void
    {
        $file = $this->fixture('parent-call/ok/src/Entities/Orders/Schema/Get/GetParams.php');
        $this->analyse([$file], []);
    }
}
