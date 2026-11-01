<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\EntityModuleLayoutRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\WritesFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<EntityModuleLayoutRule>
 */
final class EntityModuleLayoutRuleTest extends RuleTestCase
{
    use WritesFixtures;

    protected function getRule(): Rule
    {
        return new EntityModuleLayoutRule();
    }

    public function testExtraRootFile(): void
    {
        $file = $this->fixture('layout/src/Entities/Contatos/Helper.php', "<?php\n\nnamespace AleBatistella\\BlingErpApi\\Entities\\Contatos;\n\nclass Helper {}\n");
        $this->analyse([$file], [
            ['The only PHP file in src/Entities/Contatos/ is Contatos.php.', 3],
        ]);
    }

    public function testOperationFolder(): void
    {
        $file = $this->fixture('layout/ok/src/Entities/Contatos/Schema/Get/GetResponse.php', "<?php\n\nnamespace AleBatistella\\BlingErpApi\\Entities\\Contatos\\Schema\\Get;\n\nreadonly final class GetResponse {}\n");
        $this->analyse([$file], []);
    }
}
