<?php
$finder = PhpCsFixer\Finder::create()
    ->name('*.phtml')
    ->in('config')
    ->in('src')
;
global $argv;

\PhpCsFixer\Fixer\Alias\NoAliasFunctionsFixer::class;
$config = PhpCsFixer\Config::create();
\PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer::class;
$rules = [
    '@PSR1' => true,
    '@PSR2' => true,
    'lowercase_cast' => true,
    'array_syntax' => [
        'syntax' => 'short'
    ],
    'native_function_casing' => true,
    'new_with_braces' => true,
    'no_blank_lines_after_phpdoc' => true,
    'no_leading_import_slash' => true,
    'no_useless_else' => true,
    'no_useless_return' => true,
    'ordered_imports' => true,
    'phpdoc_add_missing_param_annotation' => true,
    'phpdoc_align' => true,
    'phpdoc_scalar' => true,
    'phpdoc_indent' => true,
    'phpdoc_separation' => true,
    'return_type_declaration' => true,
    'phpdoc_types' => true,
    'short_scalar_cast' => true,
    'single_quote' => true,
    'standardize_not_equals' => true,
    'ordered_class_elements' => [
        'use_trait',
        'constant_public',
        'constant_protected',
        'constant_private',
        'property_public',
        'property_protected',
        'property_private',
        'construct',
        'destruct',
        'magic',
        'phpunit',
        'method_public',
        'method_protected',
        'method_private',
    ],
    'no_mixed_echo_print' => [
        'use' => 'echo'
    ],
    'header_comment' => [
        'commentType' => \PhpCsFixer\Fixer\Comment\HeaderCommentFixer::HEADER_COMMENT,
        'header' => <<<'HEADER_COMMENT'

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
"AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

This software consists of voluntary contributions made by many individuals
and is licensed under the MIT license.
HEADER_COMMENT
    ],
];

$riskyRules = [
    'native_function_invocation' => true,
];


if (stristr(implode(' ', $argv), '--allow-risky')) {
    $rules = \array_merge($rules, $riskyRules);
}

$config->setRules($rules);
$config->setFinder($finder);
return $config;
