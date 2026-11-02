<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\ReadonlyRequestDtoRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\LoadsFixtures;
use AlexandreBellas\CodeConventions\PhpStan\Tests\NeutralConfig;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<ReadonlyRequestDtoRule>
 */
final class ReadonlyRequestDtoRuleTest extends RuleTestCase
{
    use LoadsFixtures;

    protected function getRule(): Rule
    {
        return new ReadonlyRequestDtoRule(NeutralConfig::make());
    }

    public function testFinalQuery(): void
    {
        $file = $this->fixture('request-dto/src/Entities/@shared/DTO/Request/Query.php');
        $this->analyse([$file], [
            ['Query must be a non-final `readonly class`.', 5],
        ]);
    }

    public function testReadonlyBody(): void
    {
        $file = $this->fixture('request-dto/ok/src/Entities/@shared/DTO/Request/Payload.php');
        $this->analyse([$file], []);
    }
}
