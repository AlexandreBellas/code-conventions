<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\RequireRepositoryInheritDocRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\WritesFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<RequireRepositoryInheritDocRule>
 */
final class RequireRepositoryInheritDocRuleTest extends RuleTestCase
{
    use WritesFixtures;

    protected function getRule(): Rule
    {
        return new RequireRepositoryInheritDocRule();
    }

    public function testMissingInheritDoc(): void
    {
        $file = $this->fixture('repo-inherit/bad.php', <<<'CODE'
<?php

namespace AleBatistella\BlingErpApi\Repositories;

interface IBlingRepository
{
    public function index(): void;
}

class BlingRepository implements IBlingRepository
{
    public function __construct()
    {
    }

    public function index(): void
    {
    }

    private function buildResponseBody(): void
    {
    }
}

CODE);
        $this->analyse([$file], [
            ['index implements IBlingRepository and must document @inheritDoc.', 16],
        ]);
    }

    public function testHasInheritDoc(): void
    {
        $file = $this->fixture('repo-inherit/ok.php', <<<'CODE'
<?php

namespace AleBatistella\BlingErpApi\Repositories;

interface IBlingRepository
{
    public function index(): void;
}

class BlingRepository implements IBlingRepository
{
    /**
     * @inheritDoc
     */
    public function index(): void
    {
    }
}

CODE);
        $this->analyse([$file], []);
    }
}
