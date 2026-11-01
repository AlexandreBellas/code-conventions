<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\ReadonlyFinalSchemaRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\WritesFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<ReadonlyFinalSchemaRule>
 */
final class ReadonlyFinalSchemaRuleTest extends RuleTestCase
{
    use WritesFixtures;

    protected function getRule(): Rule
    {
        return new ReadonlyFinalSchemaRule();
    }

    public function testMutableSchema(): void
    {
        $file = $this->fixture('schema-readonly/src/Entities/Contatos/Schema/Get/GetResponse.php', "<?php\n\nnamespace AleBatistella\\BlingErpApi\\Entities\\Contatos\\Schema\\Get;\n\nclass GetResponse {}\n");
        $this->analyse([$file], [
            ['Schema class GetResponse must be declared as `readonly final class`.', 5],
        ]);
    }

    public function testReadonlyFinal(): void
    {
        $file = $this->fixture('schema-readonly/ok/src/Entities/Contatos/Schema/Get/GetResponse.php', "<?php\n\nnamespace AleBatistella\\BlingErpApi\\Entities\\Contatos\\Schema\\Get;\n\nreadonly final class GetResponse {}\n");
        $this->analyse([$file], []);
    }
}
