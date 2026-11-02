<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Rules\ReadonlyFinalSchema;

use AlexandreBellas\CodeConventions\PhpStan\TestSupport\LoadsFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<ReadonlyFinalSchemaRule>
 */
final class ReadonlyFinalSchemaRuleTest extends RuleTestCase
{
    use LoadsFixtures;

    protected function getRule(): Rule
    {
        return new ReadonlyFinalSchemaRule();
    }

    public function testMutableSchema(): void
    {
        $file = $this->fixture('schema-readonly/src/Entities/Orders/Schema/Get/GetResponse.php');
        $this->analyse([$file], [
            ['Schema class GetResponse must be declared as `readonly final class`.', 5],
        ]);
    }

    public function testReadonlyFinal(): void
    {
        $file = $this->fixture('schema-readonly/ok/src/Entities/Orders/Schema/Get/GetResponse.php');
        $this->analyse([$file], []);
    }
}
