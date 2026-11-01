<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\ParamsExtendQueryParamsRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\WritesFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<ParamsExtendQueryParamsRule>
 */
final class ParamsExtendQueryParamsRuleTest extends RuleTestCase
{
    use WritesFixtures;

    protected function getRule(): Rule
    {
        return new ParamsExtendQueryParamsRule();
    }

    public function testDoesNotExtendQueryParams(): void
    {
        $file = $this->fixture('params-extend/src/Entities/Contatos/Schema/Get/GetParams.php', "<?php\n\nnamespace AleBatistella\\BlingErpApi\\Entities\\Contatos\\Schema\\Get;\n\nreadonly final class GetParams {}\n");
        $this->analyse([$file], [
            ['GetParams must extend QueryParams.', 5],
        ]);
    }

    public function testExtendsQueryParams(): void
    {
        $file = $this->fixture('params-extend/ok/src/Entities/Contatos/Schema/Get/GetParams.php', "<?php\n\nnamespace AleBatistella\\BlingErpApi\\Entities\\Contatos\\Schema\\Get;\n\nreadonly final class GetParams extends QueryParams {}\n");
        $this->analyse([$file], []);
    }
}
