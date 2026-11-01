<?php

declare(strict_types=1);

namespace AlexandreBellas\CodeConventions\PhpStan\Tests\Rules;

use AlexandreBellas\CodeConventions\PhpStan\Rules\NoHttpInEntitiesRule;
use AlexandreBellas\CodeConventions\PhpStan\Tests\WritesFixtures;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<NoHttpInEntitiesRule>
 */
final class NoHttpInEntitiesRuleTest extends RuleTestCase
{
    use WritesFixtures;

    protected function getRule(): Rule
    {
        return new NoHttpInEntitiesRule();
    }

    public function testGuzzleImport(): void
    {
        $file = $this->fixture('http/src/Entities/Contatos/Contatos.php', "<?php\n\nnamespace AleBatistella\\BlingErpApi\\Entities\\Contatos;\n\nuse GuzzleHttp\\Client;\n\nclass Contatos extends BaseEntity {}\n");
        $this->analyse([$file], [
            ['Entity code does not construct an HTTP client. Call $this->repository->index, show, store, update, replace, or destroy.', 5],
        ]);
    }

    public function testRepositoryOnly(): void
    {
        $file = $this->fixture('http/ok/src/Entities/Contatos/Contatos.php', "<?php\n\nnamespace AleBatistella\\BlingErpApi\\Entities\\Contatos;\n\nclass Contatos extends BaseEntity {}\n");
        $this->analyse([$file], []);
    }
}
