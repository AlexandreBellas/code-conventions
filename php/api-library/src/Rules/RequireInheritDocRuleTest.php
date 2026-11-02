<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\RequireInheritDocRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\LoadsFixtures;
use AlexandreBellas\CodeConventions\PhpStan\Tests\NeutralConfig;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<RequireInheritDocRule>
 */
final class RequireInheritDocRuleTest extends RuleTestCase
{
    use LoadsFixtures;

    protected function getRule(): Rule
    {
        return new RequireInheritDocRule(NeutralConfig::make());
    }

    public function testMissingInheritDoc(): void
    {
        $file = $this->fixture('inherit/bad.php');
        $this->analyse([$file], [
            ['fromResponse must document @inheritDoc.', 17],
            ['fromRules must document @inheritDoc.', 21],
        ]);
    }

    public function testHasInheritDoc(): void
    {
        $file = $this->fixture('inherit/ok.php');
        $this->analyse([$file], []);
    }

    public function testDeclarationIsNotAnImplementation(): void
    {
        $file = $this->fixture('inherit/declaration.php');
        $this->analyse([$file], []);
    }
}
