<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules\RequireRepositoryInheritDoc;

use AlexandreBellas\CodeConventions\PhpStan\TestSupport\LoadsFixtures;
use AlexandreBellas\CodeConventions\PhpStan\TestSupport\NeutralConfig;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<RequireRepositoryInheritDocRule>
 */
final class RequireRepositoryInheritDocRuleTest extends RuleTestCase
{
    use LoadsFixtures;

    protected function getRule(): Rule
    {
        return new RequireRepositoryInheritDocRule(NeutralConfig::make());
    }

    public function testMissingInheritDoc(): void
    {
        $file = $this->fixture('repo-inherit/bad.php');
        $this->analyse([$file], [
            ['index implements ApiRepository and must document @inheritDoc.', 16],
        ]);
    }

    public function testHasInheritDoc(): void
    {
        $file = $this->fixture('repo-inherit/ok.php');
        $this->analyse([$file], []);
    }
}
