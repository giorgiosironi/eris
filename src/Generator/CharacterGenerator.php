<?php
namespace Eris\Generator;

use Eris\Generator;

/**
 * Generates character in the ASCII 0-127 range.
 *
 * @param array $characterSets  Only supported charset: "basic-latin"
 * @param string $encoding  Only supported encoding: "utf-8"
 * @return Generator\CharacterGenerator
 */
function char(array $characterSets = ['basic-latin'], $encoding = 'utf-8')
{
    return CharacterGenerator::ascii();
}

/**
 * Generates character in the ASCII 32-127 range, excluding non-printable ones
 * or modifiers such as CR, LF and Tab.
 *
 * @return Generator\CharacterGenerator
 */
function charPrintableAscii()
{
    return CharacterGenerator::printableAscii();
}

class CharacterGenerator implements Generator
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

    public function __invoke($_size, $rand)
    {
        return GeneratedValueSingle::fromJustValue(chr($rand($this->lowerLimit, $this->upperLimit)), 'character');
    }

    public function shrink(GeneratedValueSingle $element)
    {
        $codePoint = ord($element->unbox());
        $shrinkedValue = chr($this->shrinkingProgression->next(ord($element->unbox())));
        return GeneratedValueSingle::fromJustValue($shrinkedValue, 'character');
    }
}
