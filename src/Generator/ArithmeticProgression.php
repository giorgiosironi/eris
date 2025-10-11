<?php
namespace Eris\Generator;

/**
 * Moves a value toward a lower limit such that the difference between two
 * members of the progression is constant (currently 1).
 *
 * TODO: GeometricProgression where the ratio between two members
 *       of the progression is constant
 */
class ArithmeticProgression
{
    public static function discrete($lowerLimit): self
    {
        return new self($lowerLimit);
    }
    
    private function __construct(private $lowerLimit)
    {
    }

    public function next($currentValue)
    {
        if ($currentValue > $this->lowerLimit) {
            return $currentValue - 1;
        }

        return $this->lowerLimit;
    }
}
