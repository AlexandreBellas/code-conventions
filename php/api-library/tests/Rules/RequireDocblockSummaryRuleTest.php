<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\RequireDocblockSummaryRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\WritesFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<RequireDocblockSummaryRule>
 */
final class RequireDocblockSummaryRuleTest extends RuleTestCase
{
    use WritesFixtures;

    protected function getRule(): Rule
    {
        return new RequireDocblockSummaryRule();
    }

    public function testMissingSummary(): void
    {
        $file = $this->fixture('summary/src/Entities/Contatos/Schema/Get/GetParams.php', "<?php\n\nnamespace AleBatistella\\BlingErpApi\\Entities\\Contatos\\Schema\\Get;\n\nreadonly final class GetParams extends QueryParams {}\n");
        $this->analyse([$file], [
            ['GetParams must have a one-line docblock summary.', 5],
        ]);
    }

    public function testHasSummary(): void
    {
        $file = $this->fixture('summary/ok/src/Entities/Contatos/Schema/Get/GetParams.php', "<?php\n\nnamespace AleBatistella\\BlingErpApi\\Entities\\Contatos\\Schema\\Get;\n\n/** Parâmetros da busca. */\nreadonly final class GetParams extends QueryParams {}\n");
        $this->analyse([$file], []);
    }
}
