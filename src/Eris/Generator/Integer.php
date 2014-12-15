<?php
namespace Eris\Generator;
use Eris\Generator;
use InvalidArgumentException;

if (!defined('ERIS_PHP_INT_MIN')) {
    define('ERIS_PHP_INT_MIN', ~PHP_INT_MAX);
}

function pos($upperLimit = PHP_INT_MAX)
{
    return new Integer(0, $upperLimit);
}

function neg($lowerLimit = ERIS_PHP_INT_MIN)
{
    return new Integer($lowerLimit, 0);
}

function int($lowerLimit = ERIS_PHP_INT_MIN, $upperLimit = PHP_INT_MAX)
{
    return new Integer($lowerLimit, $upperLimit);
}

class Integer implements Generator
{
    public function __construct($oneLimit = ERIS_PHP_INT_MIN, $otherLimit = PHP_INT_MAX)
    {
        $this->checkLimits($oneLimit, $otherLimit);

        $this->lowerLimit = min($oneLimit, $otherLimit);
        $this->upperLimit = max($oneLimit, $otherLimit);
    }

    public function __invoke()
    {
        $valueWithoutOffset = rand(0, $this->upperLimit - ($this->lowerLimit + 1));
        return $this->lowerLimit + $valueWithoutOffset;
    }

    public function shrink($element)
    {
        $this->checkValueToShrink($element);

        if ($element > 0 && $element > $this->lowerLimit) {
            return $element - 1;
        }
        if ($element < 0 && $element < $this->upperLimit) {
            return $element + 1;
        }

        return $element;
    }

    public function contains($element)
    {
        return is_int($element)
            && $element >= $this->lowerLimit
            && $element <= $this->upperLimit;
    }

    private function checkLimits($lowerLimit, $upperLimit)
    {
        if ((!is_int($lowerLimit)) || (!is_int($upperLimit))) {
            throw new InvalidArgumentException(
                "lowerLimit (" . var_export($lowerLimit, true) . ") and " .
                "upperLimit (" . var_export($upperLimit, true) . ") should " .
                "be Integers between " . ERIS_PHP_INT_MIN . " and " . PHP_INT_MAX
            );
        }
    }

    private function checkValueToShrink($value)
    {
        if (!$this->contains($value)) {
            throw new InvalidArgumentException(
                "Cannot shrink {$value} because does not belongs to the domain of " .
                "Integers between {$this->lowerLimit} and {$this->upperLimit}"
            );
        }
    }
}
