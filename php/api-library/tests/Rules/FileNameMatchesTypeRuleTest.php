<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\FileNameMatchesTypeRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\WritesFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<FileNameMatchesTypeRule>
 */
final class FileNameMatchesTypeRuleTest extends RuleTestCase
{
    use WritesFixtures;

    protected function getRule(): Rule
    {
        return new FileNameMatchesTypeRule();
    }

    public function testMismatchedName(): void
    {
        $file = $this->fixture('file-name/Foo.php', "<?php\n\nclass Bar {}\n");
        $this->analyse([$file], [
            ['File Foo.php must declare Foo so the file name matches the type name.', 3],
        ]);
    }

    public function testSpecSuffix(): void
    {
        $file = $this->fixture('file-name/FooTest.spec.php', "<?php\n\nclass FooTest {}\n");
        $this->analyse([$file], []);
    }
}
