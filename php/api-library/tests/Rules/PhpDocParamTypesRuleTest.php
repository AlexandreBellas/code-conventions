<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\PhpDocParamTypesRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\WritesFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<PhpDocParamTypesRule>
 */
final class PhpDocParamTypesRuleTest extends RuleTestCase
{
    use WritesFixtures;

    protected function getRule(): Rule
    {
        return new PhpDocParamTypesRule();
    }

    public function testTypeMismatch(): void
    {
        $file = $this->fixture('param-types/bad.php', <<<'CODE'
<?php

class GetParams
{
    /**
     * @param ?int[] $idsContatos
     */
    public function __construct(public ?array $idsContatos = null)
    {
    }
}

CODE);
        $this->analyse([$file], [
            ['@param $idsContatos of __construct must use the signature type ?array.', 8],
        ]);
    }

    public function testMatchingType(): void
    {
        $file = $this->fixture('param-types/ok.php', <<<'CODE'
<?php

class GetParams
{
    /**
     * @param ?array $idsContatos
     */
    public function __construct(public ?array $idsContatos = null)
    {
    }
}

CODE);
        $this->analyse([$file], []);
    }
}
