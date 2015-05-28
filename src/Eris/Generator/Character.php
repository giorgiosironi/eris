<?php
namespace Eris\Generator;

use Eris\Generator;

/**
 * Generates character in the ASCII 0-127 range.
 *
 * @param array $characterSets  Only supported charset: "basic-latin"
 * @param string $encoding  Only supported encoding: "utf-8"
 * @return Generator\Character
 */
function char(array $characterSets, $encoding = 'utf-8')
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
    private $upperLimit;
    private $shrinkingProgression;

    public static function ascii()
    {
        return new self($lowerLimit = 0, $upperLimit = 127);
    }

    public static function printableAscii()
    {
        return new self($lowerLimit = 32, $upperLimit = 126);
    }
    
    public function __construct($lowerLimit, $upperLimit)
    {
        $this->lowerLimit = $lowerLimit;
        $this->upperLimit = $upperLimit;
        $this->shrinkingProgression = ArithmeticProgression::discrete($this->lowerLimit);
    }

    public function __invoke()
    {
        return chr(rand($this->lowerLimit, $this->upperLimit)); 
    }

    public function shrink($value)
    {
        $codePoint = ord($value);
        $shrinkedValue = chr($this->shrinkingProgression->next(ord($value)));
        return $shrinkedValue;
    }

    public function contains($value)
    {
        return is_string($value)
            && strlen($value) == 1
            && ord($value) >= $this->lowerLimit
            && ord($value) <= $this->upperLimit;
    }
}
