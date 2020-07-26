<?php
namespace Eris\Generator;

use Eris\Generator;
use Eris\Random\RandomRange;
use phpDocumentor\Reflection\Types\Integer;

/**
 * Generates a positive or negative integer (with absolute value bounded by
 * the generation size).
 * @returns IntegerGenerator
 */
function int()
{
    return IntegerGenerator::int();
}

/**
 * Generates a positive integer (bounded by the generation size).
 * * @returns IntegerGenerator
 */
function pos()
{
    return IntegerGenerator::pos();
}

/**
 * Generates a natural number.
 * @return IntegerGenerator
 */
function nat()
{
    return IntegerGenerator::nat();
}

/**
 * Generates a negative integer (bounded by the generation size).
 * @return IntegerGenerator
 */
function neg()
{
    return IntegerGenerator::neg();
}

/**
 * Generate a byte (integer value in the interval 0 - 255)
 * @return ChooseGenerator
 */
function byte()
{
    return IntegerGenerator::byte();
}

class IntegerGenerator implements Generator
{
    private $mapFn;

    public function __construct(callable $mapFn = null)
    {
        if (is_null($mapFn)) {
            $this->mapFn = $this->identity();
        } else {
            $this->mapFn = $mapFn;
        }
    }

    public function __invoke($size, RandomRange $rand)
    {
        $value = $rand->rand(0, $size);
        $mapFn = $this->mapFn;

        $result = $rand->rand(0, 1) === 0
                          ? $mapFn($value)
                          : $mapFn($value * (-1));
        return GeneratedValueSingle::fromJustValue(
            $result,
            'integer'
        );
    }

    public function shrink(GeneratedValue $element)
    {
        $mapFn = $this->mapFn;
        $element = $element->input();

        if ($element > 0) {
            $options = [];
            $nextHalf = $element;
            while (($nextHalf = (int) floor($nextHalf / 2)) > 0) {
                $options[] = GeneratedValueSingle::fromJustValue(
                    $mapFn($element - $nextHalf),
                    'integer'
                );
            }
            $options = array_unique($options, SORT_REGULAR);
            if ($options) {
                return new GeneratedValueOptions($options);
            } else {
                return GeneratedValueSingle::fromJustValue($mapFn($element - 1), 'integer');
            }
        }
        if ($element < 0) {
            // TODO: shrink with options also negative values
            return GeneratedValueSingle::fromJustValue($mapFn($element + 1), 'integer');
        }

        return GeneratedValueSingle::fromJustValue($element, 'integer');
    }

    private function identity()
    {
        return function ($n) {
            return $n;
        };
    }

    /**
     * Generates a positive or negative integer (with absolute value bounded by
     * the generation size).
     * @returns IntegerGenerator
     */
    public static function int()
    {
        return new self();
    }

    /**
     * Generates a positive integer (bounded by the generation size).
     * * @returns IntegerGenerator
     */
    public static function pos()
    {
        $mustBeStrictlyPositive = function ($n) {
            return abs($n) + 1;
        };
        return new self($mustBeStrictlyPositive);
    }

    /**
     * Generates a natural number.
     * @return IntegerGenerator
     */
    public static function nat()
    {
        $mustBeNatural = function ($n) {
            return abs($n);
        };
        return new self($mustBeNatural);
    }

    /**
     * Generates a negative integer (bounded by the generation size).
     * @return IntegerGenerator
     */
    public static function neg()
    {
        $mustBeStrictlyNegative = function ($n) {
            return (-1) * (abs($n) + 1);
        };
        return new self($mustBeStrictlyNegative);
    }

    /**
     * Generate a byte (integer value in the interval 0 - 255)
     * @return ChooseGenerator
     */
    public static function byte()
    {
        return new ChooseGenerator(0, 255);
    }
}
