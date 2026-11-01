<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\EntityExtendsBaseRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\WritesFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<EntityExtendsBaseRule>
 */
final class EntityExtendsBaseRuleTest extends RuleTestCase
{
    use WritesFixtures;

    protected function getRule(): Rule
    {
        return new EntityExtendsBaseRule();
    }

    public function testReadonlyEntity(): void
    {
        $file = $this->fixture('entity-base/src/Entities/Contatos/Contatos.php', "<?php\n\nnamespace AleBatistella\\BlingErpApi\\Entities\\Contatos;\n\nreadonly class Contatos extends BaseEntity {}\n");
        $this->analyse([$file], [
            ['Entity class Contatos must be a concrete, non-readonly subclass of BaseEntity.', 5],
        ]);
    }

    public function testExtendsBaseEntity(): void
    {
        $file = $this->fixture('entity-base/ok/src/Entities/Contatos/Contatos.php', "<?php\n\nnamespace AleBatistella\\BlingErpApi\\Entities\\Contatos;\n\nclass Contatos extends BaseEntity {}\n");
        $this->analyse([$file], []);
    }
}
