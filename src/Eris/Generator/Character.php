<?php
namespace Eris\Generator;

class Character
{
    public static function ascii()
    {
        return new self();
    }

    public function __invoke()
    {
        return chr(rand(0, 127)); 
    }

    public function contains($value)
    {
        return is_string($value)
            && strlen($value) == 1
            && ord($value) >= 0
            && ord($value) <= 127;
    }
}
