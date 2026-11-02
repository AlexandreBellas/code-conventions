<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules\EntityRepositoryCall;

use AlexandreBellas\CodeConventions\PhpStan\TestSupport\LoadsFixtures;
use AlexandreBellas\CodeConventions\PhpStan\TestSupport\NeutralConfig;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<EntityRepositoryCallRule>
 */
final class EntityRepositoryCallRuleTest extends RuleTestCase
{
    use LoadsFixtures;

    protected function getRule(): Rule
    {
        return new EntityRepositoryCallRule(NeutralConfig::make());
    }

    public function testMissingRepositoryCall(): void
    {
        $file = $this->fixture('repository-call/src/Entities/Orders/Orders.php');
        $this->analyse([$file], [
            ['Public entity method execute must call exactly one of $this->repository->index, show, store, update, replace, or destroy.', 7],
            ['execute must return *Response::fromResponse(...), and the return type must be that response class or null.', 7],
        ]);
    }

    public function testRepositoryCall(): void
    {
        $file = $this->fixture('repository-call/ok/src/Entities/Orders/Orders.php');
        $this->analyse([$file], []);
    }
}
