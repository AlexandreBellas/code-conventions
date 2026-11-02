<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules\NoAuthorizationInEntities;

use AlexandreBellas\CodeConventions\PhpStan\TestSupport\LoadsFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<NoAuthorizationInEntitiesRule>
 */
final class NoAuthorizationInEntitiesRuleTest extends RuleTestCase
{
    use LoadsFixtures;

    protected function getRule(): Rule
    {
        return new NoAuthorizationInEntitiesRule();
    }

    public function testAuthorizationHeader(): void
    {
        $file = $this->fixture('auth/src/Entities/Orders/Orders.php');
        $this->analyse([$file], [
            ['Entities do not set Authorization. The bearer header is set in the repository.', 5],
        ]);
    }

    public function testNoHeader(): void
    {
        $file = $this->fixture('auth/ok/src/Entities/Orders/Orders.php');
        $this->analyse([$file], []);
    }
}
