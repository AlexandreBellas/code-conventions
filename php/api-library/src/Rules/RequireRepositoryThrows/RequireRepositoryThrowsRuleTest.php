<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules\RequireRepositoryThrows;

use AlexandreBellas\CodeConventions\PhpStan\TestSupport\LoadsFixtures;
use AlexandreBellas\CodeConventions\PhpStan\TestSupport\NeutralConfig;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<RequireRepositoryThrowsRule>
 */
final class RequireRepositoryThrowsRuleTest extends RuleTestCase
{
    use LoadsFixtures;

    protected function getRule(): Rule
    {
        return new RequireRepositoryThrowsRule(NeutralConfig::make());
    }

    public function testMissingThrows(): void
    {
        $file = $this->fixture('throws/src/Entities/Orders/Orders.php');
        $this->analyse([$file], [
            ['get must document `@throws AcmeApiException|AcmeInternalException`.', 7],
        ]);
    }

    public function testDocumentsThrows(): void
    {
        $file = $this->fixture('throws/ok/src/Entities/Orders/Orders.php');
        $this->analyse([$file], []);
    }
}
