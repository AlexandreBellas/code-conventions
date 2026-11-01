<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\NamespaceMirrorsPathRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\WritesFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<NamespaceMirrorsPathRule>
 */
final class NamespaceMirrorsPathRuleTest extends RuleTestCase
{
    use WritesFixtures;

    protected function getRule(): Rule
    {
        return new NamespaceMirrorsPathRule();
    }

    public function testWrongNamespace(): void
    {
        $file = $this->fixture('namespace/src/Entities/Contatos/Contatos.php', "<?php\n\nnamespace Wrong;\n\nclass Contatos {}\n");
        $this->analyse([$file], [
            ['Namespace Wrong must be AleBatistella\\BlingErpApi\\Entities\\Contatos so it mirrors the path under src/.', 3],
        ]);
    }

    public function testSharedDirectory(): void
    {
        $file = $this->fixture('namespace/ok/src/Entities/@shared/BaseEntity.php', "<?php\n\nnamespace AleBatistella\\BlingErpApi\\Entities\\Shared;\n\nclass BaseEntity {}\n");
        $this->analyse([$file], []);
    }
}
