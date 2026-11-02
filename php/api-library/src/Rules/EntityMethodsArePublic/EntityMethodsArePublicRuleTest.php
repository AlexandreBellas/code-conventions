<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules\EntityMethodsArePublic;

use AlexandreBellas\CodeConventions\PhpStan\TestSupport\LoadsFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<EntityMethodsArePublicRule>
 */
final class EntityMethodsArePublicRuleTest extends RuleTestCase
{
    use LoadsFixtures;

    protected function getRule(): Rule
    {
        return new EntityMethodsArePublicRule();
    }

    public function testPrivateMethod(): void
    {
        $file = $this->fixture('public-method/src/Entities/Orders/Orders.php');
        $this->analyse([$file], [
            ['Entity method hidden must be public.', 5],
        ]);
    }

    public function testPublicMethod(): void
    {
        $file = $this->fixture('public-method/ok/src/Entities/Orders/Orders.php');
        $this->analyse([$file], []);
    }
}
