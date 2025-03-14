<?php

declare(strict_types=1);

use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;

$finder = (new PhpCsFixer\Finder())
    ->in([
        __DIR__.'/src',
        __DIR__.'/tests',
        __DIR__.'/config',
    ])
    ->notPath('bundles.php')
    ->append([__FILE__])
;

return (new PhpCsFixer\Config())
    ->setParallelConfig(ParallelConfigFactory::detect())
    ->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'global_namespace_import' => [
            'import_classes' => true,
            'import_functions' => false,
            'import_constants' => false,
        ],
        'visibility_required' => [
            'elements' => ['const', 'property', 'method'],
        ],
        'list_syntax' => [
            'syntax' => 'short',
        ],
        'yoda_style' => [
            'less_and_greater' => false,
        ],
        'ternary_to_null_coalescing' => true,
        'array_indentation' => true,
        'explicit_string_variable' => true,
        'align_multiline_comment' => true,
        'phpdoc_order' => true,
        'phpdoc_var_annotation_correct_order' => true,
        'trailing_comma_in_multiline' => [
            'elements' => ['arrays', 'parameters', 'arguments', 'match'],
        ],
        'declare_strict_types' => true,
        'php_unit_method_casing' => ['case' => 'snake_case'],
    ])
    ->setRiskyAllowed(true)
    ->setCacheFile('var/cache/.php-cs-fixer.cache')
    ->setFinder($finder)
;
