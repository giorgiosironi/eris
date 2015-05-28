<?php
namespace Eris\Generator;

use Eris\Generator;
use InvalidArgumentException;
use DomainException;

/**
 * @param float $lowerLimit
 * @param float $upperLimit
 * @return Eris\Generator\Float
 */
function float($lowerLimit, $upperLimit)
{
    return new Float($lowerLimit, $upperLimit);
}

class Float implements Generator
{
    private $lowerLimit;
    private $upperLimit;

    public function __construct($oneLimit, $otherLimit)
    {
        $this->ensureIsNumeric($oneLimit);
        $this->ensureIsNumeric($otherLimit);

        $this->lowerLimit = min((float) $oneLimit, (float) $otherLimit);
        $this->upperLimit = max((float) $oneLimit, (float) $otherLimit);
    }

    public function __invoke()
    {
        $pointer = rand(0, getrandmax()) / getrandmax();
        $intervalLength = $this->upperLimit - $this->lowerLimit;
        $pointerOffset = $intervalLength * $pointer;
        return $this->lowerLimit + $pointerOffset;
    }

    public function shrink($element)
    {
        if (!$this->contains($element)) {
            throw new DomainException(
                'Cannot shrink ' . $element . ' because it does not belong to the domain of ' .
                'Floats between ' . $this->lowerLimit . ' and ' . $this->upperLimit
            );
        }

        if ($element < 0.0) {
            return min($element + 1.0, 0.0, $this->upperLimit);
        }
        if ($element > 0.0) {
            return max($element - 1.0, 0.0, $this->lowerLimit);
        }
        return 0.0;
    }

    public function contains($element)
    {
        return is_float($element)
            && $element >= $this->lowerLimit
            && $element <= $this->upperLimit;
    }

    private function ensureIsNumeric($value)
    {
        if (!is_numeric($value)) {
            throw new InvalidArgumentException(
                var_export($value, true) . " is not a valid numerical boundary"
            );
        }
    }
}

