<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\EnumDocblockRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\WritesFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<EnumDocblockRule>
 */
final class EnumDocblockRuleTest extends RuleTestCase
{
    use WritesFixtures;

    protected function getRule(): Rule
    {
        return new EnumDocblockRule();
    }

    public function testMissingEnumerador(): void
    {
        $file = $this->fixture('enum-doc/src/Entities/Contatos/Enum/Situacao.php', "<?php\n\nnamespace AleBatistella\\BlingErpApi\\Entities\\Contatos\\Enum;\n\nenum Situacao: string { case ATIVO = \"A\"; }\n");
        $this->analyse([$file], [
            ['Situacao must start its docblock with "Enumerador de".', 5],
        ]);
    }

    public function testEnumerador(): void
    {
        $file = $this->fixture('enum-doc/ok/src/Entities/Contatos/Enum/Situacao.php', "<?php\n\nnamespace AleBatistella\\BlingErpApi\\Entities\\Contatos\\Enum;\n\n/** Enumerador de situação. */\nenum Situacao: string { case ATIVO = \"A\"; }\n");
        $this->analyse([$file], []);
    }
}
