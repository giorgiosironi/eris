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
    /**
     * @template K of array-key
     * @template V
     * @param array<K, Generator<V>|V> $generators
     * @return AssociativeArrayGenerator<K, V>
     */
    public static function associative(array $generators)
    {
        return new AssociativeArrayGenerator($generators);
    }

    /**
     * @template TInner
     * @template T
     * @param Generator<TInner> $innerGenerator
     * @param callable(TInner): Generator<T> $outerGeneratorFactory
     * @return BindGenerator<TInner, T>
     */
    public static function bind(Generator $innerGenerator, callable $outerGeneratorFactory)
    {
        return new BindGenerator(
            $innerGenerator,
            $outerGeneratorFactory
        );
    }

    /**
     * @return BooleanGenerator
     */
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
     * @param int $lowerLimit One of the 2 boundaries of the range
     * @param int $upperLimit The other boundary of the range
     * @return Generator\ChooseGenerator
     */
    public static function choose($lowerLimit, $upperLimit)
    {
        return new ChooseGenerator($lowerLimit, $upperLimit);
    }

    /**
     * @template T
     * @param T $value the only value to generate
     * @return ConstantGenerator<T>
     */
    public static function constant($value)
    {
        return ConstantGenerator::box($value);
    }

    /**
     * @param \DateTime|string|null $lowerLimit
     * @param \DateTime|string|null $upperLimit
     * @return DateGenerator
     */
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

    /**
     * elements($a, $b, ...) or elements([$a, $b, ...])
     *
     * @phpstan-template TFirst
     * @phpstan-template TMore
     * @param mixed $elementOrElements the array of all elements, or the first of several elements
     * @param mixed ...$moreElements
     * @phpstan-param TFirst $elementOrElements
     * @phpstan-param TMore ...$moreElements
     * @return Generator\ElementsGenerator<mixed>
     * @phpstan-return ($moreElements is array{} ? Generator\ElementsGenerator<value-of<TFirst>> : Generator\ElementsGenerator<TFirst|TMore>)
     */
    public static function elements($elementOrElements, ...$moreElements)
    {
        if ($moreElements === []) {
            return Generator\ElementsGenerator::fromArray($elementOrElements);
        }
        return Generator\ElementsGenerator::fromArray([$elementOrElements, ...$moreElements]);
    }

    /**
     * @return FloatGenerator
     */
    public static function float()
    {
        return new FloatGenerator();
    }

    /**
     * frequency([$frequency, $generator], [$frequency, $generator], ...)
     *
     * @template T
     * @param array{int<0, max>, Generator<T>|T} ...$frequencyAndGenerator
     * @return FrequencyGenerator<T>
     */
    public static function frequency(array ...$frequencyAndGenerator)
    {
        return new FrequencyGenerator($frequencyAndGenerator);
    }

    /**
     * Generates a positive or negative integer (with absolute value bounded by
     * the generation size).
     *
     * @return IntegerGenerator<int>
     */
    public static function int()
    {
        return new IntegerGenerator();
    }

    /**
     * Generates a positive integer (bounded by the generation size).
     *
     * @return IntegerGenerator<int<1, max>>
     */
    public static function pos()
    {
        $mustBeStrictlyPositive = function (int $n) {
            return abs($n) + 1;
        };
        return new IntegerGenerator($mustBeStrictlyPositive);
    }

    /**
     * @return IntegerGenerator<int<0, max>>
     */
    public static function nat()
    {
        $mustBeNatural = function (int $n) {
            return abs($n);
        };
        return new IntegerGenerator($mustBeNatural);
    }

    /**
     * Generates a negative integer (bounded by the generation size).
     *
     * @return IntegerGenerator<int<min, -1>>
     */
    public static function neg()
    {
        $mustBeStrictlyNegative = function (int $n) {
            return (-1) * (abs($n) + 1);
        };
        return new IntegerGenerator($mustBeStrictlyNegative);
    }

    /**
     * @return ChooseGenerator
     */
    public static function byte()
    {
        return new ChooseGenerator(0, 255);
    }

    /**
     * @template T
     * @template U
     * @param callable(T): U $function
     * @param Generator<T> $generator
     * @return MapGenerator<T, U>
     */
    public static function map(callable $function, Generator $generator)
    {
        return new MapGenerator($function, $generator);
    }

    /**
     * @return NamesGenerator
     */
    public static function names()
    {
        return NamesGenerator::defaultDataSet();
    }

    /**
     * @template T
     * @param Generator<T>|T ...$_generators
     * @return OneOfGenerator<T>
     */
    public static function oneOf(...$_generators)
    {
        return new OneOfGenerator($_generators);
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

    /**
     * @template T
     * @param Generator<T> $singleElementGenerator
     * @return SequenceGenerator<T>
     */
    public static function seq(Generator $singleElementGenerator)
    {
        return new SequenceGenerator($singleElementGenerator);
    }

    /**
     * @template T
     * @param Generator<T> $singleElementGenerator
     * @return SetGenerator<T>
     */
    public static function set($singleElementGenerator)
    {
        return new SetGenerator($singleElementGenerator);
    }

    /**
     * @return StringGenerator
     */
    public static function string()
    {
        return new StringGenerator();
    }

    /**
     * @template T
     * @param array<T> $input
     * @return SubsetGenerator<T>
     */
    public static function subset($input)
    {
        return new SubsetGenerator($input);
    }

    /**
     * @template T
     * @param (callable(T): bool)|Constraint $filter
     * @param Generator<T> $generator
     * @param int $maximumAttempts
     * @return SuchThatGenerator<T>
     */
    public static function filter($filter, Generator $generator, $maximumAttempts = 100)
    {
        return self::suchThat($filter, $generator, $maximumAttempts);
    }

    /**
     * @template T
     * @param (callable(T): bool)|Constraint $filter
     * @param Generator<T> $generator
     * @param int $maximumAttempts
     * @return SuchThatGenerator<T>
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
     *
     * @phpstan-template T
     * @param mixed $generatorOrGenerators
     * @param mixed ...$moreGenerators
     * @phpstan-param array<Generator<T>|T>|Generator<T>|T $generatorOrGenerators
     * @phpstan-param Generator<T>|T ...$moreGenerators
     * @return Generator\TupleGenerator<mixed>
     * @phpstan-return Generator\TupleGenerator<T>
     */
    public static function tuple($generatorOrGenerators = [], ...$moreGenerators)
    {
        if (is_array($generatorOrGenerators)) {
            $generators = $generatorOrGenerators;
        } else {
            $generators = [$generatorOrGenerators, ...$moreGenerators];
        }
        return new TupleGenerator($generators);
    }

    /**
     * @template T
     * @param int $size
     * @param Generator<T> $elementsGenerator
     * @return VectorGenerator<T>
     */
    public static function vector($size, Generator $elementsGenerator)
    {
        return new VectorGenerator($size, $elementsGenerator);
    }
}
