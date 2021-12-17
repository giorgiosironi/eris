<?php

namespace Eris;

use Eris\Generator\AssociativeArrayGenerator;
use Eris\Generator\BindGenerator;
use Eris\Generator\BooleanGenerator;
use Eris\Generator\CharacterGenerator;
use Eris\Generator\ChooseGenerator;
use Eris\Generator\ConstantGenerator;
use Eris\Generator\DateGenerator;
use Eris\Generator\FloatGenerator;
use Eris\Generator\FrequencyGenerator;
use Eris\Generator\IntegerGenerator;
use Eris\Generator\MapGenerator;
use Eris\Generator\NamesGenerator;
use Eris\Generator\OneOfGenerator;
use Eris\Generator\RegexGenerator;
use Eris\Generator\SequenceGenerator;
use Eris\Generator\SetGenerator;
use Eris\Generator\StringGenerator;
use Eris\Generator\SubsetGenerator;
use Eris\Generator\SuchThatGenerator;
use Eris\Generator\TupleGenerator;
use Eris\Generator\VectorGenerator;
use PHPUnit\Framework\Constraint\Constraint;

final class Generators
{
    public static function associative(array $generators)
    {
        return new AssociativeArrayGenerator($generators);
    }

    public static function bind(Generator $innerGenerator, callable $outerGeneratorFactory)
    {
        return new BindGenerator(
            $innerGenerator,
            $outerGeneratorFactory
        );
    }

    public static function bool()
    {
        return new BooleanGenerator();
    }

    /**
     * Generates character in the ASCII 0-127 range.
     *
     * @param array $characterSets Only supported charset: "basic-latin"
     * @param string $encoding Only supported encoding: "utf-8"
     * @return Generator\CharacterGenerator
     */
    public static function char(array $characterSets = ['basic-latin'], $encoding = 'utf-8')
    {
        return CharacterGenerator::ascii();
    }

    /**
     * Generates character in the ASCII 32-127 range, excluding non-printable ones
     * or modifiers such as CR, LF and Tab.
     *
     * @return Generator\CharacterGenerator
     */
    public static function charPrintableAscii()
    {
        return CharacterGenerator::printableAscii();
    }

    /**
     * Generates a number in the range from the lower bound to the upper bound,
     * inclusive. The result shrinks towards smaller absolute values.
     * The order of the parameters does not care since they are re-ordered by the
     * generator itself.
     *
     * @param $x int One of the 2 boundaries of the range
     * @param $y int The other boundary of the range
     * @return Generator\ChooseGenerator
     */
    public static function choose($lowerLimit, $upperLimit)
    {
        return new ChooseGenerator($lowerLimit, $upperLimit);
    }

    /**
     * @param mixed $value the only value to generate
     * @return ConstantGenerator
     */
    public static function constant($value)
    {
        return ConstantGenerator::box($value);
    }

    public static function date($lowerLimit = null, $upperLimit = null)
    {
        $box = function ($date) {
            if ($date === null) {
                return $date;
            }
            if ($date instanceof \DateTime) {
                return $date;
            }
            return new \DateTime($date);
        };
        $withDefault = function ($value, $default) {
            if ($value !== null) {
                return $value;
            }
            return $default;
        };
        return new DateGenerator(
            $withDefault($box($lowerLimit), new \DateTime("@0")),
            // uses a maximum which is conservative
            $withDefault($box($upperLimit), new \DateTime("@" . (pow(2, 31) - 1)))
        );
    }

    public static function elements(/*$a, $b, ...*/)
    {
        $arguments = func_get_args();
        if (count($arguments) == 1) {
            return Generator\ElementsGenerator::fromArray($arguments[0]);
        } else {
            return Generator\ElementsGenerator::fromArray($arguments);
        }
    }

    public static function float()
    {
        return new FloatGenerator();
    }

    /**
     * @return FrequencyGenerator
     */
    public static function frequency(/*$frequencyAndGenerator, $frequencyAndGenerator, ...*/)
    {
        return new FrequencyGenerator(func_get_args());
    }

    /**
     * Generates a positive or negative integer (with absolute value bounded by
     * the generation size).
     */
    public static function int()
    {
        return new IntegerGenerator();
    }

    /**
     * Generates a positive integer (bounded by the generation size).
     */
    public static function pos()
    {
        $mustBeStrictlyPositive = function ($n) {
            return abs($n) + 1;
        };
        return new IntegerGenerator($mustBeStrictlyPositive);
    }

    public static function nat()
    {
        $mustBeNatural = function ($n) {
            return abs($n);
        };
        return new IntegerGenerator($mustBeNatural);
    }

    /**
     * Generates a negative integer (bounded by the generation size).
     */
    public static function neg()
    {
        $mustBeStrictlyNegative = function ($n) {
            return (-1) * (abs($n) + 1);
        };
        return new IntegerGenerator($mustBeStrictlyNegative);
    }

    public static function byte()
    {
        return new ChooseGenerator(0, 255);
    }

    public static function map(callable $function, Generator $generator)
    {
        return new MapGenerator($function, $generator);
    }

    public static function names()
    {
        return NamesGenerator::defaultDataSet();
    }

    /**
     * @return OneOfGenerator
     */
    public static function oneOf(/*$a, $b, ...*/)
    {
        return new OneOfGenerator(func_get_args());
    }

    /**
     * Note * and + modifiers cause an unbounded number of character to be generated
     * (up to plus infinity) and as such they are not supported.
     * Please use {1,N} and {0,N} instead of + and *.
     *
     * @param string $expression
     * @return Generator\RegexGenerator
     */
    public static function regex($expression)
    {
        return new RegexGenerator($expression);
    }

    public static function seq(Generator $singleElementGenerator)
    {
        return new SequenceGenerator($singleElementGenerator);
    }

    /**
     * @param Generator $singleElementGenerator
     * @return SetGenerator
     */
    public static function set($singleElementGenerator)
    {
        return new SetGenerator($singleElementGenerator);
    }

    public static function string()
    {
        return new StringGenerator();
    }

    /**
     * @param array $input
     * @return SubsetGenerator
     */
    public static function subset($input)
    {
        return new SubsetGenerator($input);
    }

    /**
     * @param callable|Constraint $filter
     * @return SuchThatGenerator
     */
    public static function filter($filter, Generator $generator, $maximumAttempts = 100)
    {
        return self::suchThat($filter, $generator, $maximumAttempts);
    }

    /**
     * @param callable|Constraint $filter
     * @return SuchThatGenerator
     */
    public static function suchThat($filter, Generator $generator, $maximumAttempts = 100)
    {
        return new SuchThatGenerator($filter, $generator, $maximumAttempts);
    }

    /**
     * One Generator for each member of the Tuple:
     * tuple(Generator, Generator, Generator...)
     * Or an array of generators:
     * tuple(array $generators)
     * @return Generator\TupleGenerator
     */
    public static function tuple()
    {
        $arguments = func_get_args();
        if (is_array($arguments[0])) {
            $generators = $arguments[0];
        } else {
            $generators = $arguments;
        }
        return new TupleGenerator($generators);
    }

    public static function vector($size, Generator $elementsGenerator)
    {
        return new VectorGenerator($size, $elementsGenerator);
    }
}
