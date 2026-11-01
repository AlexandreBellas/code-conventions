<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\RequireEntityReturnDocRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\WritesFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<RequireEntityReturnDocRule>
 */
final class RequireEntityReturnDocRuleTest extends RuleTestCase
{
    use WritesFixtures;

    protected function getRule(): Rule
    {
        return new RequireEntityReturnDocRule();
    }

    public function testMissingReturn(): void
    {
        $file = $this->fixture('return-doc/src/Entities/Contatos/Contatos.php', "<?php\n\nnamespace AleBatistella\\BlingErpApi\\Entities\\Contatos;\n\nclass Contatos extends BaseEntity { public function get(): GetResponse { return GetResponse::fromResponse(\$response); } }\n");
        $this->analyse([$file], [
            ['get must document @return.', 5],
        ]);
    }

    public function testHasReturn(): void
    {
        $file = $this->fixture('return-doc/ok/src/Entities/Contatos/Contatos.php', "<?php\n\nnamespace AleBatistella\\BlingErpApi\\Entities\\Contatos;\n\nclass Contatos extends BaseEntity { /** @return GetResponse */ public function get(): GetResponse { return GetResponse::fromResponse(\$response); } }\n");
        $this->analyse([$file], []);
    }
}
