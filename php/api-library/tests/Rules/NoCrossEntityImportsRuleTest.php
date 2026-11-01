<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\NoCrossEntityImportsRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\WritesFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<NoCrossEntityImportsRule>
 */
final class NoCrossEntityImportsRuleTest extends RuleTestCase
{
    use WritesFixtures;

    protected function getRule(): Rule
    {
        return new NoCrossEntityImportsRule();
    }

    public function testImportsAnotherModule(): void
    {
        $file = $this->fixture('cross/src/Entities/Contatos/Contatos.php', "<?php\n\nnamespace AleBatistella\\BlingErpApi\\Entities\\Contatos;\n\nuse AleBatistella\\BlingErpApi\\Entities\\Produtos\\Produtos;\n\nclass Contatos extends BaseEntity {}\n");
        $this->analyse([$file], [
            ['This import resolves into entity Produtos. One resource does not import another. Use this module or Entities\\Shared.', 5],
        ]);
    }

    public function testImportsShared(): void
    {
        $file = $this->fixture('cross/ok/src/Entities/Contatos/Contatos.php', "<?php\n\nnamespace AleBatistella\\BlingErpApi\\Entities\\Contatos;\n\nuse AleBatistella\\BlingErpApi\\Entities\\Shared\\BaseEntity;\n\nclass Contatos extends BaseEntity {}\n");
        $this->analyse([$file], []);
    }
}
