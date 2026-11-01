<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\RequireGetInstanceRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\WritesFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<RequireGetInstanceRule>
 */
final class RequireGetInstanceRuleTest extends RuleTestCase
{
    use WritesFixtures;

    protected function getRule(): Rule
    {
        return new RequireGetInstanceRule();
    }

    public function testMissingGetInstance(): void
    {
        $file = $this->fixture('get-instance/src/Entities/Contatos/__tests__/ContatosTest.spec.php', "<?php\n\nnamespace Tests\\Unit\\AleBatistella\\BlingErpApi\\Entities\\Contatos;\n\nclass ContatosTest extends TestCase {}\n");
        $this->analyse([$file], [
            ['An entity spec defines private function getInstance(IBlingRepository $repository).', 5],
        ]);
    }

    public function testHasGetInstance(): void
    {
        $file = $this->fixture('get-instance/ok/src/Entities/Contatos/__tests__/ContatosTest.spec.php', "<?php\n\nnamespace Tests\\Unit\\AleBatistella\\BlingErpApi\\Entities\\Contatos;\n\nclass ContatosTest extends TestCase { private function getInstance(IBlingRepository \$repository): Contatos { return new Contatos(\$repository); } }\n");
        $this->analyse([$file], []);
    }
}
