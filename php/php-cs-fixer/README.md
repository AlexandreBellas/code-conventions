# PHP-CS-Fixer api-library

Formatting for the same API client preset. Run it from the project root so the finder sees `src/`.

`.php-cs-fixer.php`:

```php
<?php

return require __DIR__ . '/vendor/alexandrebellas/code-conventions/php/php-cs-fixer/api-library.php';
```

```bash
vendor/bin/php-cs-fixer fix
```

| Fixer | Enforces |
| --- | --- |
| `indentation_type` | Indent with spaces. The indent unit is 4 spaces. |
| `statement_indentation` | Statement nesting uses that 4-space indent. |
| `line_ending` | LF line endings. |
| `single_blank_line_at_eof` | The file ends with a newline. |
| `no_closing_tag` | No closing `?>`. |
| `blank_line_after_opening_tag` | `<?php` is followed by a blank line. |
| `single_import_per_statement` | One `use` per line. Group imports are split. |
| `group_import: false` | Imports are not collapsed into groups. |
| `no_unused_imports` | A `use` names a symbol the file references. |
| `nullable_type_declaration_for_default_null_value` | A parameter defaulting to `null` uses nullable syntax (`?Type $name = null`). |
| `nullable_type_declaration` (`question_mark`) | A single nullable type is `?int`. Unions of more than two types stay unions. |

`ordered_imports` is off. Double quotes are a PHPStan rule, not a fixer. See [php/api-library/README.md](../api-library/README.md).
