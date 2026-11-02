<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\FileNameMatchesTypeRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\LoadsFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<FileNameMatchesTypeRule>
 */
final class FileNameMatchesTypeRuleTest extends RuleTestCase
{
    use LoadsFixtures;

    protected function getRule(): Rule
    {
        return new FileNameMatchesTypeRule();
    }

    public function testMismatchedName(): void
    {
        $file = $this->fixture('file-name/Foo.php');
        $this->analyse([$file], [
            ['File Foo.php must declare Foo so the file name matches the type name.', 3],
        ]);
    }

    public function testSpecSuffix(): void
    {
        $file = $this->fixture('file-name/FooTest.spec.php');
        $this->analyse([$file], []);
    }
}
