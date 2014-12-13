<?php
namespace Eris\Generator;
use Eris\Generator;

function float($lowerLimit, $upperLimit)
{
    return new Float($lowerLimit, $upperLimit);
}

class Float implements Generator
{
    private $lowerLimit;
    private $upperLimit;

    public function __construct($lowerLimit, $upperLimit)
    {
        $this->lowerLimit = $lowerLimit;
        $this->upperLimit = $upperLimit;
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
}

