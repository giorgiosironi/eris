<?php
namespace Eris\Generator;
use Eris\Generator;

/**
 * Generates character in the ASCII 0-127 range.
 *
 * @return Generator\Character
 */
function charAscii()
{
    return Character::ascii();
}

/**
 * Generates character in the ASCII 32-127 range, excluding non-printable ones
 * or modifiers such as CR, LF and Tab.
 *
 * @return Generator\Character
 */
function charPrintableAscii()
{
    return Character::printableAscii();
}

class Character implements Generator
{
    private $lowerLimit;

    public static function ascii()
    {
        return new self($lowerLimit = 0);
    }

    public static function printableAscii()
    {
        return new self($lowerLimit = 32);
    }
    
    private function __construct($lowerLimit)
    {
        $this->lowerLimit = $lowerLimit;
    }

    public function __invoke()
    {
        return chr(rand($this->lowerLimit, 127)); 
    }

    public function shrink($value)
    {
        return $value;
    }

    public function contains($value)
    {
        return is_string($value)
            && strlen($value) == 1
            && ord($value) >= $this->lowerLimit
            && ord($value) <= 127;
    }
}
