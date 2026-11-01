<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\EntityMethodsArePublicRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\WritesFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<EntityMethodsArePublicRule>
 */
final class EntityMethodsArePublicRuleTest extends RuleTestCase
{
    use WritesFixtures;

    protected function getRule(): Rule
    {
        return new EntityMethodsArePublicRule();
    }

    public function testPrivateMethod(): void
    {
        $file = $this->fixture('public-method/src/Entities/Contatos/Contatos.php', "<?php\n\nnamespace AleBatistella\\BlingErpApi\\Entities\\Contatos;\n\nclass Contatos extends BaseEntity { private function hidden(): void {} }\n");
        $this->analyse([$file], [
            ['Entity method hidden must be public.', 5],
        ]);
    }

    public function testPublicMethod(): void
    {
        $file = $this->fixture('public-method/ok/src/Entities/Contatos/Contatos.php', "<?php\n\nnamespace AleBatistella\\BlingErpApi\\Entities\\Contatos;\n\nclass Contatos extends BaseEntity { public function get(): void {} }\n");
        $this->analyse([$file], []);
    }
}
