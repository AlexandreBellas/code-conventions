<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$root = getcwd();
$src = $root . DIRECTORY_SEPARATOR . 'src';

$finder = Finder::create()
    ->in(is_dir($src) ? $src : $root)
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new Config())
    ->setRiskyAllowed(true)
    ->setIndent('    ')
    ->setLineEnding("\n")
    ->setRules([
        'indentation_type' => true,
        'statement_indentation' => true,
        'line_ending' => true,
        'single_blank_line_at_eof' => true,
        'no_closing_tag' => true,
        'blank_line_after_opening_tag' => true,
        'single_import_per_statement' => ['group_to_single_imports' => true],
        'group_import' => false,
        'no_unused_imports' => true,
        'nullable_type_declaration_for_default_null_value' => true,
        'nullable_type_declaration' => ['syntax' => 'question_mark'],
    ])
    ->setFinder($finder);
