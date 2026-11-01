<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\ParamsCallParentRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\WritesFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<ParamsCallParentRule>
 */
final class ParamsCallParentRuleTest extends RuleTestCase
{
    use WritesFixtures;

    protected function getRule(): Rule
    {
        return new ParamsCallParentRule();
    }

    public function testMissingParentCall(): void
    {
        $file = $this->fixture('parent-call/src/Entities/Contatos/Schema/Get/GetParams.php', <<<'CODE'
<?php

namespace AleBatistella\BlingErpApi\Entities\Contatos\Schema\Get;

readonly final class GetParams extends QueryParams
{
    public function __construct(public ?int $pagina = null)
    {
    }
}

CODE);
        $this->analyse([$file], [
            ['GetParams::__construct must finish with parent::__construct(objectToArray($this)).', 7],
        ]);
    }

    public function testCallsParent(): void
    {
        $file = $this->fixture('parent-call/ok/src/Entities/Contatos/Schema/Get/GetParams.php', <<<'CODE'
<?php

namespace AleBatistella\BlingErpApi\Entities\Contatos\Schema\Get;

readonly final class GetParams extends QueryParams
{
    public function __construct(public ?int $pagina = null)
    {
        parent::__construct(objectToArray($this));
    }
}

CODE);
        $this->analyse([$file], []);
    }
}
