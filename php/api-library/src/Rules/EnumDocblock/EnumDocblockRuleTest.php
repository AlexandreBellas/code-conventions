<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules\EnumDocblock;

use AlexandreBellas\CodeConventions\PhpStan\TestSupport\LoadsFixtures;
use AlexandreBellas\CodeConventions\PhpStan\TestSupport\NeutralConfig;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<EnumDocblockRule>
 */
final class EnumDocblockRuleTest extends RuleTestCase
{
    use LoadsFixtures;

    protected function getRule(): Rule
    {
        return new EnumDocblockRule(NeutralConfig::make());
    }

    public function testMissingPrefix(): void
    {
        $file = $this->fixture('enum-doc/src/Entities/Orders/Enum/Status.php');
        $this->analyse([$file], [
            ['Status must start its docblock with "Set of".', 5],
        ]);
    }

    public function testHasPrefix(): void
    {
        $file = $this->fixture('enum-doc/ok/src/Entities/Orders/Enum/Status.php');
        $this->analyse([$file], []);
    }
}
