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
    public static function associative(array $generators): \Eris\Generator\AssociativeArrayGenerator
    {
        return new AssociativeArrayGenerator($generators);
    }

    public static function bind(Generator $innerGenerator, callable $outerGeneratorFactory): \Eris\Generator\BindGenerator
    {
        return new BindGenerator(
            $innerGenerator,
            $outerGeneratorFactory
        );
    }

    public static function bool(): \Eris\Generator\BooleanGenerator
    {
        return new BooleanGenerator();
    }

    /**
     * Generates character in the ASCII 0-127 range.
     */
    public static function char(): \Eris\Generator\CharacterGenerator
    {
        return CharacterGenerator::ascii();
    }

    /**
     * Generates character in the ASCII 32-127 range, excluding non-printable ones
     * or modifiers such as CR, LF and Tab.
     */
    public static function charPrintableAscii(): \Eris\Generator\CharacterGenerator
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
     */
    public static function choose($lowerLimit, $upperLimit): \Eris\Generator\ChooseGenerator
    {
        return new ChooseGenerator($lowerLimit, $upperLimit);
    }

    /**
     * @param mixed $value the only value to generate
     */
    public static function constant($value): \Eris\Generator\ConstantGenerator
    {
        return ConstantGenerator::box($value);
    }

    public static function date($lowerLimit = null, $upperLimit = null): \Eris\Generator\DateGenerator
    {
        $box = function ($date): ?\DateTime {
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
            $withDefault($box($upperLimit), new \DateTime("@" . (2 ** 31 - 1)))
        );
    }

    public static function elements(/*$a, $b, ...*/): \Eris\Generator\ElementsGenerator
    {
        $arguments = func_get_args();
        if (count($arguments) === 1) {
            return Generator\ElementsGenerator::fromArray($arguments[0]);
        }
        return Generator\ElementsGenerator::fromArray($arguments);
    }

    public static function float(): \Eris\Generator\FloatGenerator
    {
        return new FloatGenerator();
    }

    public static function frequency(/*$frequencyAndGenerator, $frequencyAndGenerator, ...*/): \Eris\Generator\FrequencyGenerator
    {
        return new FrequencyGenerator(func_get_args());
    }

    /**
     * Generates a positive or negative integer (with absolute value bounded by
     * the generation size).
     */
    public static function int(): \Eris\Generator\IntegerGenerator
    {
        return new IntegerGenerator();
    }

    /**
     * Generates a positive integer (bounded by the generation size).
     */
    public static function pos(): \Eris\Generator\IntegerGenerator
    {
        $mustBeStrictlyPositive = (fn($n): float|int => abs($n) + 1);
        return new IntegerGenerator($mustBeStrictlyPositive);
    }

    public static function nat(): \Eris\Generator\IntegerGenerator
    {
        $mustBeNatural = (fn($n): float|int => abs($n));
        return new IntegerGenerator($mustBeNatural);
    }

    /**
     * Generates a negative integer (bounded by the generation size).
     */
    public static function neg(): \Eris\Generator\IntegerGenerator
    {
        $mustBeStrictlyNegative = (fn($n): float|int => (-1) * (abs($n) + 1));
        return new IntegerGenerator($mustBeStrictlyNegative);
    }

    public static function byte(): \Eris\Generator\ChooseGenerator
    {
        return new ChooseGenerator(0, 255);
    }

    public static function map(callable $function, Generator $generator): \Eris\Generator\MapGenerator
    {
        return new MapGenerator($function, $generator);
    }

    public static function names()
    {
        return NamesGenerator::defaultDataSet();
    }

    public static function oneOf(...$_generators): \Eris\Generator\OneOfGenerator
    {
        return new OneOfGenerator(func_get_args());
    }

    /**
     * Note * and + modifiers cause an unbounded number of character to be generated
     * (up to plus infinity) and as such they are not supported.
     * Please use {1,N} and {0,N} instead of + and *.
     *
     * @param string $expression
     */
    public static function regex($expression): \Eris\Generator\RegexGenerator
    {
        return new RegexGenerator($expression);
    }

    public static function seq(Generator $singleElementGenerator): \Eris\Generator\SequenceGenerator
    {
        return new SequenceGenerator($singleElementGenerator);
    }

    /**
     * @param Generator $singleElementGenerator
     */
    public static function set($singleElementGenerator): \Eris\Generator\SetGenerator
    {
        return new SetGenerator($singleElementGenerator);
    }

    public static function string(): \Eris\Generator\StringGenerator
    {
        return new StringGenerator();
    }

    /**
     * @param array $input
     */
    public static function subset($input): \Eris\Generator\SubsetGenerator
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
     */
    public static function suchThat($filter, Generator $generator, $maximumAttempts = 100): \Eris\Generator\SuchThatGenerator
    {
        return new SuchThatGenerator($filter, $generator, $maximumAttempts);
    }

    /**
     * One Generator for each member of the Tuple:
     * tuple(Generator, Generator, Generator...)
     * Or an array of generators:
     * tuple(array $generators)
     */
    public static function tuple(): \Eris\Generator\TupleGenerator
    {
        $arguments = func_get_args();
        $generators = is_array($arguments[0]) ? $arguments[0] : $arguments;
        return new TupleGenerator($generators);
    }

    public static function vector($size, Generator $elementsGenerator): \Eris\Generator\VectorGenerator
    {
        return new VectorGenerator($size, $elementsGenerator);
    }
}
