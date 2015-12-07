<?php
namespace Eris\Generator;

class GeneratedValue
{
    private $value;
    private $input;
    
    public static function fromValueAndInput($value, $input)
    {
        return new self($value, $input);
    }
    
    private function __construct($value, $input)
    {
        $this->value = $value;
        $this->input = $input;
    }
}
