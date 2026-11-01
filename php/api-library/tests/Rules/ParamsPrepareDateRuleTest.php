<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\ParamsPrepareDateRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\WritesFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<ParamsPrepareDateRule>
 */
final class ParamsPrepareDateRuleTest extends RuleTestCase
{
    use WritesFixtures;

    protected function getRule(): Rule
    {
        return new ParamsPrepareDateRule();
    }

    public function testRawDate(): void
    {
        $file = $this->fixture('prepare-date/src/Entities/Contatos/Schema/Get/GetParams.php', <<<'CODE'
<?php

namespace AleBatistella\BlingErpApi\Entities\Contatos\Schema\Get;

readonly final class GetParams extends QueryParams
{
    public function __construct(\DateTimeInterface|string|null $dataInclusaoInicial = null)
    {
        parent::__construct(objectToArray($this));
    }
}

CODE);
        $this->analyse([$file], [
            ['$dataInclusaoInicial must be passed through prepareStringOrDateParam.', 7],
        ]);
    }

    public function testPreparedDate(): void
    {
        $file = $this->fixture('prepare-date/ok/src/Entities/Contatos/Schema/Get/GetParams.php', <<<'CODE'
<?php

namespace AleBatistella\BlingErpApi\Entities\Contatos\Schema\Get;

readonly final class GetParams extends QueryParams
{
    public function __construct(\DateTimeInterface|string|null $dataInclusaoInicial = null)
    {
        $this->dataInclusaoInicial = $this->prepareStringOrDateParam($dataInclusaoInicial, true);
        parent::__construct(objectToArray($this));
    }
}

CODE);
        $this->analyse([$file], []);
    }
}
