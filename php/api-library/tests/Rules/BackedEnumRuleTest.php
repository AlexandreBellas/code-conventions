<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\BackedEnumRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\WritesFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<BackedEnumRule>
 */
final class BackedEnumRuleTest extends RuleTestCase
{
    use WritesFixtures;

    protected function getRule(): Rule
    {
        return new BackedEnumRule();
    }

    public function testPureEnum(): void
    {
        $file = $this->fixture('backed/src/Entities/Contatos/Enum/Situacao.php', "<?php\n\nnamespace AleBatistella\\BlingErpApi\\Entities\\Contatos\\Enum;\n\nenum Situacao { case ATIVO; }\n");
        $this->analyse([$file], [
            ['Situacao must be a backed enum (`enum Situacao: string` or `: int`).', 5],
        ]);
    }

    public function testStringBacked(): void
    {
        $file = $this->fixture('backed/ok/src/Entities/Contatos/Enum/Situacao.php', "<?php\n\nnamespace AleBatistella\\BlingErpApi\\Entities\\Contatos\\Enum;\n\nenum Situacao: string { case ATIVO = \"A\"; }\n");
        $this->analyse([$file], []);
    }
}
