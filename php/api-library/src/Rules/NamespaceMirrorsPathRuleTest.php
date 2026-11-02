<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\NamespaceMirrorsPathRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\LoadsFixtures;
use AlexandreBellas\CodeConventions\PhpStan\Tests\NeutralConfig;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<NamespaceMirrorsPathRule>
 */
final class NamespaceMirrorsPathRuleTest extends RuleTestCase
{
    use LoadsFixtures;

    protected function getRule(): Rule
    {
        return new NamespaceMirrorsPathRule(NeutralConfig::make());
    }

    public function testWrongNamespace(): void
    {
        $file = $this->fixture('namespace/src/Entities/Orders/Orders.php');
        $this->analyse([$file], [
            ['Namespace Wrong must be Acme\\ApiClient\\Entities\\Orders so it mirrors the path under src/.', 3],
        ]);
    }

    public function testSharedDirectory(): void
    {
        $file = $this->fixture('namespace/ok/src/Entities/@shared/Entity.php');
        $this->analyse([$file], []);
    }
}
