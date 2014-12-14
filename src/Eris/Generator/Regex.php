<?php
namespace Eris\Generator;
use Eris\Generator;
use ReverseRegex\Lexer;
use ReverseRegex\Random\SimpleRandom;
use ReverseRegex\Parser;
use ReverseRegex\Generator\Scope;

function regex($expression)
{
    return new Regex($expression);
}

class Regex implements Generator
{
    private $expression;
    
    public function __construct($expression)
    {
        if (!class_exists("ReverseRegex\Parser")) {
            throw new BadFunctionCallException("Please install the suggested dependency icomefromthenet/reverse-regex to run this Generator.");
        }
        $this->expression = $expression;
    }

    public function __invoke()
    {
        $lexer = new Lexer($this->expression);
        $gen   = new SimpleRandom(rand());
        $result = null;

        $parser = new Parser($lexer,new Scope(),new Scope());
        $parser->parse()->getResult()->generate($result,$gen);

        return $result;
    }

    public function shrink($value)
    {
        return $value;
    }

    public function contains($value)
    {
        return is_string($value)
            && (bool) preg_match("/{$this->expression}/", $value);
    }
}
