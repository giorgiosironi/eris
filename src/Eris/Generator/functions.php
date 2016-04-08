<?php
namespace Eris\Generator;

use DateTime;
use Eris\Generator;

function ensureAreAllGenerators(array $generators)
{
    return array_map('Eris\Generator\ensureIsGenerator', $generators);
}

function ensureIsGenerator($generator)
{
    if ($generator instanceof Generator) {
        return $generator;
    }
    return new ConstantGenerator($generator);
}

/**
 * @return AssociativeArrayGenerator
 */
function associative(array $generators)
{
    return new AssociativeArrayGenerator($generators);
}

function bind(Generator $innerGenerator, callable $outerGeneratorFactory)
{
    return new BindGenerator(
        $innerGenerator,
        $outerGeneratorFactory
    );
}

function bool()
{
    return new BooleanGenerator();
}

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
 * Generates a number in the range from the lower bound to the upper bound,
 * inclusive. The result shrinks towards smaller absolute values.
 * The order of the parameters does not care since they are re-ordered by the
 * generator itself.
 *
 * @param $x int One of the 2 boundaries of the range
 * @param $y int The other boundary of the range
 * @return Generator\ChooseGenerator
 */
function choose($lowerLimit, $upperLimit) {
    return new ChooseGenerator($lowerLimit, $upperLimit);
}

/**
 * @param mixed $value  the only value to generate
 * @return ConstantGenerator
 */
function constant($value)
{
    return ConstantGenerator::box($value);
}

function date($lowerLimit = null, $upperLimit = null)
{
    $box = function($date) {
        if ($date === null) {
            return $date;
        }
        if ($date instanceof DateTime) {
            return $date;
        }
        return new DateTime($date);
    };
    $withDefault = function($value, $default) {
        if ($value !== null) {
            return $value;
        }
        return $default;
    };
    return new DateGenerator(
        $withDefault($box($lowerLimit), new DateTime("@0")),
        $withDefault($box($upperLimit), new DateTime("@" . getrandmax()))
    );
}

function elements(/*$a, $b, ...*/)
{
    $arguments = func_get_args();
    if (count($arguments) == 1) {
        return Generator\ElementsGenerator::fromArray($arguments[0]);
    } else {
        return Generator\ElementsGenerator::fromArray($arguments);
    }
}

function float()
{
    return new FloatGenerator();
}

/**
 * @return FrequencyGenerator
 */
function frequency(/*$frequencyAndGenerator, $frequencyAndGenerator, ...*/)
{
    return new FrequencyGenerator(func_get_args());
}

/**
 * Generates a positive or negative integer (with absolute value bounded by
 * the generation size).
 */
function int()
{
    return new IntegerGenerator();
}

/**
 * Generates a positive integer (bounded by the generation size).
 */
function pos()
{
    $mustBeStrictlyPositive = function($n) {
        return abs($n) + 1;
    };
    return new IntegerGenerator($mustBeStrictlyPositive);
}

function nat()
{
    $mustBeNatural = function($n) {
        return abs($n);
    };
    return new IntegerGenerator($mustBeNatural);
}

/**
 * Generates a negative integer (bounded by the generation size).
 */
function neg()
{
    $mustBeStrictlyNegative = function($n) {
        return (-1) * (abs($n) + 1);
    };
    return new IntegerGenerator($mustBeStrictlyNegative);
}

function byte()
{
    return new ChooseGenerator(0, 255);
}

// TODO: support calls like ($function . $generator)
function map(callable $function, Generator $generator)
{
    return new MapGenerator($function, $generator);
}

function names()
{
    return NamesGenerator::defaultDataSet();
}

/**
 * @return OneOfGenerator
 */
function oneOf(/*$a, $b, ...*/)
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
function regex($expression)
{
    return new RegexGenerator($expression);
}

function seq(Generator $singleElementGenerator)
{
    // TODO: Generator::box($singleElementGenerator);
    if (!($singleElementGenerator instanceof Generator)) {
        $singleElementGenerator = new Constant($singleElementGenerator);
    }
    return new SequenceGenerator($singleElementGenerator);
}

/**
 * @param Generator $singleElementGenerator
 * @return SetGenerator
 */
function set($singleElementGenerator)
{
    return new SetGenerator($singleElementGenerator);
}

function string()
{
    return new StringGenerator();
}

/**
 * @param array $universe
 * @return SubsetGenerator
 */
function subset($input)
{
    return new SubsetGenerator($input);
}

/**
 * @return SuchThatGenerator
 */
function filter(callable $filter, Generator $generator)
{
    return suchThat($filter, $generator);
}

/**
 * @return SuchThatGenerator
 */
function suchThat(callable $filter, Generator $generator)
{
    return new SuchThatGenerator($filter, $generator);
}

/**
 * One Generator for each member of the Tuple:
 * tuple(Generator, Generator, Generator...)
 * Or an array of generators:
 * tuple(array $generators)
 * @return Generator\TupleGenerator
 */
function tuple()
{
    $arguments = func_get_args();
    if (is_array($arguments[0])) {
        $generators = $arguments[0];
    } else {
        $generators = $arguments;
    }
    return new TupleGenerator($generators);
}

function vector($size, Generator $elementsGenerator)
{
    return new VectorGenerator($size, $elementsGenerator);
}
