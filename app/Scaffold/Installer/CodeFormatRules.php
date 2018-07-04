<?php
return [
    'array_syntax'                       => [
        'syntax' => 'short'
    ],
    'blank_line_after_namespace'         => true,
    'single_quote'                       => true,
    'elseif'                             => true,
    'encoding'                           => true,
    'full_opening_tag'                   => true,
    'function_declaration'               => true,
    'braces'                             => [
        'allow_single_line_closure'                   => true,
        'position_after_anonymous_constructs'         => 'next',//, 'same' : whether the opening brace should be placed on "next" or "same" line after anonymous constructs (anonymous classes and lambda functions); defaults to 'same'
        'position_after_control_structures'           => 'same',//, 'next' : whether the opening brace should be placed on "next" or "same" line after control structures; defaults to 'same'
        'position_after_functions_and_oop_constructs' => 'next', //'same'): whether the opening brace should be placed on "next" or "same" line after classy constructs (non-anonymous classes, interfaces, traits, methods and non-lambda functions); defaults to 'next'
    ],
    'class_definition'                   => [
        'multiLineExtendsEachSingleLine' => false,// (bool): whether definitions should be multiline; defaults to false
        'singleItemSingleLine'           => false,// (bool): whether definitions should be single line when including a single item; defaults to false
        'singleLine'                     => false,// (bool): whether definitions should be single line; defaults to false
    ],
    'binary_operator_spaces'             => [
        'align_double_arrow' => true,// (false, null, true): whether to apply, remove or ignore double arrows alignment; defaults to false
        'align_equals'       => true,// (false, null, true): whether to apply, remove or ignore equals alignment; defaults to false
    ],
    'doctrine_annotation_indentation'    => [
        'ignored_tags'       => [],
        'indent_mixed_lines' => false
    ],
    'indentation_type'                   => true,
    'align_multiline_comment'            => [
        'comment_type' => 'all_multiline',// 'phpdocs_like', 'phpdocs_only'): whether to fix PHPDoc comments only (phpdocs_only), any multi-line comment whose lines all start with an asterisk (phpdocs_like) or any multi-line comment (all_multiline); defaults to 'phpdocs_only'
    ],
    'line_ending'                        => true,
    'lowercase_constants'                => true,
    'lowercase_keywords'                 => true,
    'method_argument_space'              => [
        'ensure_fully_multiline'           => false,// (bool): ensure every argument of a multiline argument list is on its own line; defaults to false
        'keep_multiple_spaces_after_comma' => false,// (bool): whether keep multiple spaces after comma; defaults to false
    ],
    'method_separation'                  => true,
    'no_break_comment'                   => true,
    'no_closing_tag'                     => true,
    'no_spaces_after_function_name'      => true,
    'no_spaces_inside_parenthesis'       => true,
    'no_trailing_whitespace'             => true,
    'no_trailing_whitespace_in_comment'  => true,
    'object_operator_without_whitespace' => true,
//    'psr4'                               => true,
    'single_blank_line_at_eof'           => true,
    'single_class_element_per_statement' => true,
    'single_import_per_statement'        => true,
    'single_line_after_imports'          => true,
    'switch_case_semicolon_to_colon'     => true,
    'switch_case_space'                  => true,
    'trailing_comma_in_multiline_array'  => true,
    'trim_array_spaces'                  => true,
    'visibility_required'                => true,
];