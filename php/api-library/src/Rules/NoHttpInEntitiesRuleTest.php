<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\NoHttpInEntitiesRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\LoadsFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<NoHttpInEntitiesRule>
 */
final class NoHttpInEntitiesRuleTest extends RuleTestCase
{
    use LoadsFixtures;

    protected function getRule(): Rule
    {
        return new NoHttpInEntitiesRule();
    }

    public function testGuzzleImport(): void
    {
        $file = $this->fixture('http/src/Entities/Orders/Orders.php');
        $this->analyse([$file], [
            ['Entity code does not construct an HTTP client. Call $this->repository->index, show, store, update, replace, or destroy.', 5],
        ]);
    }

    public function testRepositoryOnly(): void
    {
        $file = $this->fixture('http/ok/src/Entities/Orders/Orders.php');
        $this->analyse([$file], []);
    }
}
