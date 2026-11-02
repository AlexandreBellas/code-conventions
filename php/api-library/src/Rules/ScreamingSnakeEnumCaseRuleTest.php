<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\ScreamingSnakeEnumCaseRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\LoadsFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<ScreamingSnakeEnumCaseRule>
 */
final class ScreamingSnakeEnumCaseRuleTest extends RuleTestCase
{
    use LoadsFixtures;

    protected function getRule(): Rule
    {
        return new ScreamingSnakeEnumCaseRule();
    }

    public function testCamelCase(): void
    {
        $file = $this->fixture('snake/src/Entities/Orders/Enum/Status.php');
        $this->analyse([$file], [
            ['Enum case Active must be SCREAMING_SNAKE.', 5],
        ]);
    }

    public function testScreamingSnake(): void
    {
        $file = $this->fixture('snake/ok/src/Entities/Orders/Enum/Status.php');
        $this->analyse([$file], []);
    }
}
