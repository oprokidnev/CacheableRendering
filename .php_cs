<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->name('*.phtml')
    ->in('src')
//    ->in('view')
//    ->in('tests')
    ->in('config');
$config = Symfony\CS\Config\Config::create();

\Symfony\CS\Fixer\Contrib\HeaderCommentFixer::setHeader(<<<EOD
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
EOD
);
$config->level(null);
$config->fixers(
    array(
        'header_comment',
        'braces',
        'duplicate_semicolon',
        'elseif',
        'empty_return',
        'encoding',
        'eof_ending',
        'function_call_space',
        'function_declaration',
        'indentation',
        'join_function',
        'line_after_namespace',
        'linefeed',
        'lowercase_keywords',
        'parenthesis',
        'multiple_use',
        'method_argument_space',
        'object_operator',
        'php_closing_tag',
        'remove_lines_between_uses',
        'short_array_syntax',
        'short_tag',
        'standardize_not_equal',
        'trailing_spaces',
        'unused_use',
        'visibility',
        'whitespacy_lines',
    )
);
$config->finder($finder);
return $config;
