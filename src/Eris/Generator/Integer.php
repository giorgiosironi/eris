<?php
namespace Eris\Generator;

use Eris\Generator;
use InvalidArgumentException;
use DomainException;

/**
 * Generates a positive or negative integer (with absolute value bounded by
 * the generation size).
 */
function int()
{
    return new Integer();
}

/**
 * Generates a positive integer (bounded by the generation size).
 */
function pos()
{
    $mustBePositive = function($n) {
        return abs($n);
    };
    return new Integer($mustBePositive);
}

function nat()
{
    return pos();
}

/**
 * Generates a negative integer (bounded by the generation size).
 */
function neg()
{
    $mustBeNegative = function($n) {
        if ($n > 0) {
            return $n * (-1);
        }
        return $n;
    };
    return new Integer($mustBeNegative);
}

function byte()
{
    return new Choose(0, 255);
}

class Integer implements Generator
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

        return rand(0, 1) === 0
                          ? $mapFn($value)
                          : $mapFn($value * (-1));
    }

    public function shrink($element)
    {
        $this->checkValueToShrink($element);

        if ($element > 0) {
            return $element - 1;
        }
        if ($element < 0) {
            return $element + 1;
        }

        return $element;
    }

    public function contains($element)
    {
        return is_int($element);
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
