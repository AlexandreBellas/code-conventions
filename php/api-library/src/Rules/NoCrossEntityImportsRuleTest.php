<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\NoCrossEntityImportsRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\LoadsFixtures;
use AlexandreBellas\CodeConventions\PhpStan\Tests\NeutralConfig;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<NoCrossEntityImportsRule>
 */
final class NoCrossEntityImportsRuleTest extends RuleTestCase
{
    use LoadsFixtures;

    protected function getRule(): Rule
    {
        return new NoCrossEntityImportsRule(NeutralConfig::make());
    }

    public function testImportsAnotherModule(): void
    {
        $file = $this->fixture('cross/src/Entities/Orders/Orders.php');
        $this->analyse([$file], [
            ['This import resolves into entity Customers. One resource does not import another. Use this module or Entities\\Shared.', 5],
        ]);
    }

    public function testImportsShared(): void
    {
        $file = $this->fixture('cross/ok/src/Entities/Orders/Orders.php');
        $this->analyse([$file], []);
    }
}
