<?php
namespace Eris\Generator;
use Eris\Generator;

define('PHP_INT_MIN', ~PHP_INT_MAX);

function pos($upperLimit = PHP_INT_MAX)
{
    return new Integer(0, $upperLimit);
}

function neg($lowerLimit = PHP_INT_MIN)
{
    return new Integer($lowerLimit, 0);
}

function int($lowerLimit = PHP_INT_MIN, $upperLimit = PHP_INT_MAX)
{
    return new Integer($lowerLimit, $upperLimit);
}

class Integer implements Generator
{
    public function __construct($lowerLimit = PHP_INT_MIN, $upperLimit = PHP_INT_MAX)
    {
        if ($lowerLimit < PHP_INT_MIN || $upperLimit > PHP_INT_MAX) {
            throw new InvalidArgumentException('
                Integer generator lower limit must be >= ' . PHP_INT_MIN .
                ' and upper limit must be <= ' . PHP_INT_MAX
            );
        }
        $this->lowerLimit = $lowerLimit;
        $this->upperLimit = $upperLimit;
    }

    public function __invoke()
    {
        $possibleValues = [
            rand($this->lowerLimit, 0),
            rand(0, $this->upperLimit)
        ];
        $index = array_rand($possibleValues);
        return $possibleValues[$index];
    }

    public function shrink($element)
    {
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
}
