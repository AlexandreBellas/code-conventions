<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\ScreamingSnakeEnumCaseRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\WritesFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<ScreamingSnakeEnumCaseRule>
 */
final class ScreamingSnakeEnumCaseRuleTest extends RuleTestCase
{
    use WritesFixtures;

    protected function getRule(): Rule
    {
        return new ScreamingSnakeEnumCaseRule();
    }

    public function testCamelCase(): void
    {
        $file = $this->fixture('snake/src/Entities/Contatos/Enum/Situacao.php', "<?php\n\nnamespace AleBatistella\\BlingErpApi\\Entities\\Contatos\\Enum;\n\nenum Situacao: string { case Ativo = \"A\"; }\n");
        $this->analyse([$file], [
            ['Enum case Ativo must be SCREAMING_SNAKE.', 5],
        ]);
    }

    public function testScreamingSnake(): void
    {
        $file = $this->fixture('snake/ok/src/Entities/Contatos/Enum/Situacao.php', "<?php\n\nnamespace AleBatistella\\BlingErpApi\\Entities\\Contatos\\Enum;\n\nenum Situacao: string { case SEM_MOVIMENTACAO = \"S\"; }\n");
        $this->analyse([$file], []);
    }
}
