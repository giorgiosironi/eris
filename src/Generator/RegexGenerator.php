<?php
namespace Eris\Generator;

use BadFunctionCallException;
use Eris\Generator;
use ReverseRegex\Lexer;
use ReverseRegex\Random\SimpleRandom;
use ReverseRegex\Parser;
use ReverseRegex\Generator\Scope;

/**
 * Note * and + modifiers cause an unbounded number of character to be generated
 * (up to plus infinity) and as such they are not supported.
 * Please use {1,N} and {0,N} instead of + and *.
 *
 * @param string $expression
 * @return Generator\RegexGenerator
 */
function regex($expression)
{
    return new RegexGenerator($expression);
}

class RegexGenerator implements Generator
{
    private $expression;

    public function __construct($expression)
    {
        if (!class_exists("ReverseRegex\Parser")) {
            throw new BadFunctionCallException("Please install the suggested dependency icomefromthenet/reverse-regex to run this Generator.");
        }
        $this->expression = $expression;
    }

    public function __invoke($_size, $rand)
    {
        $lexer = new Lexer($this->expression);
        $gen   = new SimpleRandom($rand());
        $result = null;

        $parser = new Parser($lexer, new Scope(), new Scope());
        $parser->parse()->getResult()->generate($result, $gen);

        return GeneratedValueSingle::fromJustValue($result, 'regex');
    }

    public function shrink(GeneratedValueSingle $value)
    {
        return $value;
    }
}
