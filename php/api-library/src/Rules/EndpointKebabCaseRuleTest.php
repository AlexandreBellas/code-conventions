<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\EndpointKebabCaseRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\LoadsFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<EndpointKebabCaseRule>
 */
final class EndpointKebabCaseRuleTest extends RuleTestCase
{
    use LoadsFixtures;

    protected function getRule(): Rule
    {
        return new EndpointKebabCaseRule();
    }

    public function testCamelEndpoint(): void
    {
        $file = $this->fixture('endpoint/src/Entities/Orders/Orders.php');
        $this->analyse([$file], [
            ['Endpoint segment "ordersTipos" must be kebab-case.', 9],
        ]);
    }

    public function testKebabEndpoint(): void
    {
        $file = $this->fixture('endpoint/ok/src/Entities/Orders/Orders.php');
        $this->analyse([$file], []);
    }
}
