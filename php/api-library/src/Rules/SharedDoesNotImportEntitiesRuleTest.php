<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\SharedDoesNotImportEntitiesRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\LoadsFixtures;
use AlexandreBellas\CodeConventions\PhpStan\Tests\NeutralConfig;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<SharedDoesNotImportEntitiesRule>
 */
final class SharedDoesNotImportEntitiesRuleTest extends RuleTestCase
{
    use LoadsFixtures;

    protected function getRule(): Rule
    {
        return new SharedDoesNotImportEntitiesRule(NeutralConfig::make());
    }

    public function testSharedImportsModule(): void
    {
        $file = $this->fixture('shared-import/src/Entities/@shared/Entity.php');
        $this->analyse([$file], [
            ['@shared and Helpers do not import entity Orders.', 5],
        ]);
    }

    public function testHelperImportsShared(): void
    {
        $file = $this->fixture('shared-import/ok/src/Helpers/functions.php');
        $this->analyse([$file], []);
    }
}
