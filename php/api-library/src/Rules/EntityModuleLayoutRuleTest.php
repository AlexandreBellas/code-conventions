<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\EntityModuleLayoutRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\LoadsFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<EntityModuleLayoutRule>
 */
final class EntityModuleLayoutRuleTest extends RuleTestCase
{
    use LoadsFixtures;

    protected function getRule(): Rule
    {
        return new EntityModuleLayoutRule();
    }

    public function testExtraRootFile(): void
    {
        $file = $this->fixture('layout/src/Entities/Orders/Helper.php');
        $this->analyse([$file], [
            ['The only PHP file in src/Entities/Orders/ is Orders.php.', 3],
        ]);
    }

    public function testOperationFolder(): void
    {
        $file = $this->fixture('layout/ok/src/Entities/Orders/Schema/Get/GetResponse.php');
        $this->analyse([$file], []);
    }
}
