<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\OneTypePerFileRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\WritesFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<OneTypePerFileRule>
 */
final class OneTypePerFileRuleTest extends RuleTestCase
{
    use WritesFixtures;

    protected function getRule(): Rule
    {
        return new OneTypePerFileRule();
    }

    public function testTwoClasses(): void
    {
        $file = $this->fixture('one-type/bad.php', "<?php\n\nclass Alpha {}\nclass Beta {}\n");
        $this->analyse([$file], [
            ['A file declares one class, enum, interface, or trait.', 4],
        ]);
    }

    public function testOneClass(): void
    {
        $file = $this->fixture('one-type/ok.php', "<?php\n\nclass Alpha {}\n");
        $this->analyse([$file], []);
    }
}
