<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\PhpDocParamNamesRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\WritesFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<PhpDocParamNamesRule>
 */
final class PhpDocParamNamesRuleTest extends RuleTestCase
{
    use WritesFixtures;

    protected function getRule(): Rule
    {
        return new PhpDocParamNamesRule();
    }

    public function testWrongParamName(): void
    {
        $file = $this->fixture('param-names/bad.php', <<<'CODE'
<?php

class Contatos
{
    /**
     * @param int $other
     */
    public function find(int $idContato): void
    {
    }
}

CODE);
        $this->analyse([$file], [
            ['@param $other does not match a parameter of find.', 8],
            ['Parameter $idContato of find is missing an @param tag with that name.', 8],
        ]);
    }

    public function testMatchingParamName(): void
    {
        $file = $this->fixture('param-names/ok.php', <<<'CODE'
<?php

class Contatos
{
    /**
     * @param int $idContato
     */
    public function find(int $idContato): void
    {
    }
}

CODE);
        $this->analyse([$file], []);
    }
}
