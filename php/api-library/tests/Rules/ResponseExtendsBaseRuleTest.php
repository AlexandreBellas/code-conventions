<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\ResponseExtendsBaseRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\WritesFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<ResponseExtendsBaseRule>
 */
final class ResponseExtendsBaseRuleTest extends RuleTestCase
{
    use WritesFixtures;

    protected function getRule(): Rule
    {
        return new ResponseExtendsBaseRule();
    }

    public function testResponseDataExtendsWrongBase(): void
    {
        $file = $this->fixture('response-base/src/Entities/Contatos/Schema/Get/GetResponseData.php', "<?php\n\nnamespace AleBatistella\\BlingErpApi\\Entities\\Contatos\\Schema\\Get;\n\nreadonly final class GetResponseData {}\n");
        $this->analyse([$file], [
            ['GetResponseData must extend BaseResponseObject.', 5],
        ]);
    }

    public function testResponseExtendsRoot(): void
    {
        $file = $this->fixture('response-base/ok/src/Entities/Contatos/Schema/Get/GetResponse.php', "<?php\n\nnamespace AleBatistella\\BlingErpApi\\Entities\\Contatos\\Schema\\Get;\n\nreadonly final class GetResponse extends BaseResponseRootObject {}\n");
        $this->analyse([$file], []);
    }
}
