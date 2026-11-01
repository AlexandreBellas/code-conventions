<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\ReadonlyRequestDtoRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\WritesFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<ReadonlyRequestDtoRule>
 */
final class ReadonlyRequestDtoRuleTest extends RuleTestCase
{
    use WritesFixtures;

    protected function getRule(): Rule
    {
        return new ReadonlyRequestDtoRule();
    }

    public function testFinalQueryParams(): void
    {
        $file = $this->fixture('request-dto/src/Entities/@shared/DTO/Request/QueryParams.php', "<?php\n\nnamespace AleBatistella\\BlingErpApi\\Entities\\Shared\\DTO\\Request;\n\nreadonly final class QueryParams {}\n");
        $this->analyse([$file], [
            ['QueryParams must be a non-final `readonly class`.', 5],
        ]);
    }

    public function testReadonlyBody(): void
    {
        $file = $this->fixture('request-dto/ok/src/Entities/@shared/DTO/Request/Body.php', "<?php\n\nnamespace AleBatistella\\BlingErpApi\\Entities\\Shared\\DTO\\Request;\n\nreadonly class Body {}\n");
        $this->analyse([$file], []);
    }
}
