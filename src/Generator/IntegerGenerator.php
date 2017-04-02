<?php
namespace Eris\Generator;

use Eris\Generator;
use DomainException;

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
    $mustBeStrictlyPositive = function ($n) {
        return abs($n) + 1;
    };
    return new IntegerGenerator($mustBeStrictlyPositive);
}

function nat()
{
    $mustBeNatural = function ($n) {
        return abs($n);
    };
    return new IntegerGenerator($mustBeNatural);
}

/**
 * Generates a negative integer (bounded by the generation size).
 */
function neg()
{
    $mustBeStrictlyNegative = function ($n) {
        return (-1) * (abs($n) + 1);
    };
    return new IntegerGenerator($mustBeStrictlyNegative);
}

function byte()
{
    return new ChooseGenerator(0, 255);
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

    public function __invoke($size, $rand)
    {
        $value = $rand(0, $size);
        $mapFn = $this->mapFn;

        $result = $rand(0, 1) === 0
                          ? $mapFn($value)
                          : $mapFn($value * (-1));
        return GeneratedValueSingle::fromJustValue(
            $result,
            'integer'
        );
    }

    public function shrink(GeneratedValueSingle $element)
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
}
