<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\RequireFacadeRegistrationRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\WritesFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<RequireFacadeRegistrationRule>
 */
final class RequireFacadeRegistrationRuleTest extends RuleTestCase
{
    use WritesFixtures;

    protected function getRule(): Rule
    {
        return new RequireFacadeRegistrationRule();
    }

    public function testMissingFacade(): void
    {
        $file = $this->fixture('facade/src/Entities/Contatos/Contatos.php', "<?php\n\nnamespace AleBatistella\\BlingErpApi\\Entities\\Contatos;\n\nclass Contatos extends BaseEntity {}\n");
        $this->analyse([$file], [
            ['Contatos is not registered because src/Bling.php is missing.', 3],
        ]);
    }

    public function testRegistered(): void
    {
        $root = 'facade-ok/src';
        $this->fixture($root . '/Bling.php', <<<'CODE'
<?php

namespace AleBatistella\BlingErpApi;

use AleBatistella\BlingErpApi\Entities\Contatos\Contatos;

/**
 * @property Contatos $contatos
 */
class Bling
{
    public function __get(string $name)
    {
        return match ($name) {
            'contatos' => $this->getModule(Contatos::class),
        };
    }
}

CODE);
        $file = $this->fixture($root . '/Entities/Contatos/Contatos.php', "<?php\n\nnamespace AleBatistella\\BlingErpApi\\Entities\\Contatos;\n\nclass Contatos extends BaseEntity {}\n");
        $this->analyse([$file], []);
    }
}
