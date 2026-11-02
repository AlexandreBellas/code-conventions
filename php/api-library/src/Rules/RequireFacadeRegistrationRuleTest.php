<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\RequireFacadeRegistrationRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\LoadsFixtures;
use AlexandreBellas\CodeConventions\PhpStan\Tests\NeutralConfig;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<RequireFacadeRegistrationRule>
 */
final class RequireFacadeRegistrationRuleTest extends RuleTestCase
{
    use LoadsFixtures;

    protected function getRule(): Rule
    {
        return new RequireFacadeRegistrationRule(NeutralConfig::make());
    }

    public function testMissingFacade(): void
    {
        $file = $this->fixture('facade/src/Entities/Orders/Orders.php');
        $this->analyse([$file], [
            ['Orders is not registered because src/Client.php is missing.', 3],
        ]);
    }

    public function testRegistered(): void
    {
        $this->fixture('facade-ok/src/Client.php');
        $file = $this->fixture('facade-ok/src/Entities/Orders/Orders.php');
        $this->analyse([$file], []);
    }
}
