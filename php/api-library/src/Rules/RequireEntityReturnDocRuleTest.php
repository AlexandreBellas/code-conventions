<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\RequireEntityReturnDocRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\LoadsFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<RequireEntityReturnDocRule>
 */
final class RequireEntityReturnDocRuleTest extends RuleTestCase
{
    use LoadsFixtures;

    protected function getRule(): Rule
    {
        return new RequireEntityReturnDocRule();
    }

    public function testMissingReturn(): void
    {
        $file = $this->fixture('return-doc/src/Entities/Orders/Orders.php');
        $this->analyse([$file], [
            ['get must document @return.', 5],
        ]);
    }

    public function testHasReturn(): void
    {
        $file = $this->fixture('return-doc/ok/src/Entities/Orders/Orders.php');
        $this->analyse([$file], []);
    }
}
