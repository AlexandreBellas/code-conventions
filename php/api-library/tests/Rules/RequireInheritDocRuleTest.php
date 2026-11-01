<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\RequireInheritDocRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\WritesFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<RequireInheritDocRule>
 */
final class RequireInheritDocRuleTest extends RuleTestCase
{
    use WritesFixtures;

    protected function getRule(): Rule
    {
        return new RequireInheritDocRule();
    }

    public function testMissingInheritDoc(): void
    {
        $file = $this->fixture('inherit/bad.php', <<<'CODE'
<?php

interface ResponseContract
{
    public static function fromResponse(): void;
}

class BaseResponse
{
    public static function fromRules(): void
    {
    }
}

class GetResponse extends BaseResponse implements ResponseContract
{
    public static function fromResponse(): void
    {
    }

    public static function fromRules(): void
    {
    }
}

CODE);
        $this->analyse([$file], [
            ['fromResponse must document @inheritDoc.', 17],
            ['fromRules must document @inheritDoc.', 21],
        ]);
    }

    public function testHasInheritDoc(): void
    {
        $file = $this->fixture('inherit/ok.php', <<<'CODE'
<?php

interface ResponseContract
{
    public static function fromResponse(): void;
}

class BaseResponse
{
    public static function fromRules(): void
    {
    }
}

class GetResponse extends BaseResponse implements ResponseContract
{
    /**
     * @inheritDoc
     */
    public static function fromResponse(): void
    {
    }

    /**
     * @inheritDoc
     */
    public static function fromRules(): void
    {
    }
}

CODE);
        $this->analyse([$file], []);
    }

    public function testDeclarationIsNotAnImplementation(): void
    {
        $file = $this->fixture('inherit/declaration.php', <<<'CODE'
<?php

interface ResponseContract
{
    public static function fromResponse(): void;
}

class BaseResponse
{
    public static function fromRules(): void
    {
    }
}

CODE);
        $this->analyse([$file], []);
    }
}
