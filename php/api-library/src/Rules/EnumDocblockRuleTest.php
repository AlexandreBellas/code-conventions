<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\EnumDocblockRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\LoadsFixtures;
use AlexandreBellas\CodeConventions\PhpStan\Tests\NeutralConfig;
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
