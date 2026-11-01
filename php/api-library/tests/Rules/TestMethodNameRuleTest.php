<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\TestMethodNameRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\WritesFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<TestMethodNameRule>
 */
final class TestMethodNameRuleTest extends RuleTestCase
{
    use WritesFixtures;

    protected function getRule(): Rule
    {
        return new TestMethodNameRule();
    }

    public function testWrongMethodName(): void
    {
        $file = $this->fixture('test-name/src/Entities/Contatos/__tests__/ContatosTest.spec.php', <<<'CODE'
<?php

namespace Tests\Unit\AleBatistella\BlingErpApi\Entities\Contatos;

class ContatosTest extends TestCase
{
    public function testGet(): void
    {
    }
}

CODE);
        $this->analyse([$file], [
            ['testGet must be named testShould<Action>Successfully.', 7],
        ]);
    }

    public function testSuccessfulName(): void
    {
        $file = $this->fixture('test-name/ok/src/Entities/Contatos/__tests__/ContatosTest.spec.php', <<<'CODE'
<?php

namespace Tests\Unit\AleBatistella\BlingErpApi\Entities\Contatos;

class ContatosTest extends TestCase
{
    public function testShouldGetSuccessfully(): void
    {
    }
}

CODE);
        $this->analyse([$file], []);
    }
}
