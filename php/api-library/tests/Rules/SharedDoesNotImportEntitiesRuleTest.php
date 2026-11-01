<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\SharedDoesNotImportEntitiesRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\WritesFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<SharedDoesNotImportEntitiesRule>
 */
final class SharedDoesNotImportEntitiesRuleTest extends RuleTestCase
{
    use WritesFixtures;

    protected function getRule(): Rule
    {
        return new SharedDoesNotImportEntitiesRule();
    }

    public function testSharedImportsModule(): void
    {
        $file = $this->fixture('shared-import/src/Entities/@shared/BaseEntity.php', "<?php\n\nnamespace AleBatistella\\BlingErpApi\\Entities\\Shared;\n\nuse AleBatistella\\BlingErpApi\\Entities\\Contatos\\Contatos;\n\nclass BaseEntity {}\n");
        $this->analyse([$file], [
            ['@shared and Helpers do not import entity Contatos.', 5],
        ]);
    }

    public function testHelperImportsShared(): void
    {
        $file = $this->fixture('shared-import/ok/src/Helpers/functions.php', "<?php\n\nnamespace AleBatistella\\BlingErpApi\\Helpers;\n\nuse AleBatistella\\BlingErpApi\\Entities\\Shared\\BaseEntity;\n");
        $this->analyse([$file], []);
    }
}
