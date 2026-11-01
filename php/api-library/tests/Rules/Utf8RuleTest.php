<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\Utf8Rule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\WritesFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<Utf8Rule>
 */
final class Utf8RuleTest extends RuleTestCase
{
    use WritesFixtures;

    protected function getRule(): Rule
    {
        return new Utf8Rule();
    }

    public function testInvalidUtf8(): void
    {
        $file = $this->fixture('utf8/bad.php', "<?php\n// \xFF\n");
        $this->analyse([$file], [
            ['Files are UTF-8.', 2],
        ]);
    }

    public function testUtf8(): void
    {
        $file = $this->fixture('utf8/ok.php', "<?php\n// café\n");
        $this->analyse([$file], []);
    }
}
