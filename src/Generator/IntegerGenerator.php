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

    public function __invoke($size)
    {
        $value = rand(0, $size);
        $mapFn = $this->mapFn;

        $result = rand(0, 1) === 0
                          ? $mapFn($value)
                          : $mapFn($value * (-1));
        return GeneratedValue::fromJustValue(
            $result,
            'integer'
        );
    }

    public function shrink(GeneratedValue $element)
    {
        $this->checkValueToShrink($element);
        $element = $element->input();

        if ($element > 0) {
            return GeneratedValue::fromJustValue($element - 1, 'integer');
        }
        if ($element < 0) {
            return GeneratedValue::fromJustValue($element + 1, 'integer');
        }

        return GeneratedValue::fromJustValue($element, 'integer');
    }

    public function contains(GeneratedValue $element)
    {
        return is_int($element->input());
    }

    private function checkValueToShrink($value)
    {
        if (!$this->contains($value)) {
            throw new DomainException(
                'Cannot shrink ' . $value . ' because it does not belong to ' .
                'the domain of Integers'
            );
        }
    }

    private function identity()
    {
        return function($n) {
            return $n;
        };
    }
}
