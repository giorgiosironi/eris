<?php
namespace Eris\Generator;
use Eris\Generator;

function nat($upperLimit = PHP_INT_MAX)
{
    return new Natural(0, $upperLimit);
}

class Natural implements Generator
{
    public function __construct($lowerLimit, $upperLimit)
    {
        if ($lowerLimit < 0) {
            throw new InvalidArgumentException('Natural generator lower limit must be >= 0');
        }
        $this->lowerLimit = $lowerLimit;
        $this->upperLimit = $upperLimit;
    }

    public function __invoke()
    {
        return rand($this->lowerLimit, $this->upperLimit);
    }

    public function shrink($element)
    {
        if ($element > $this->lowerLimit) {
            $element--;
        }

        return $element;
    }

    public function contains($element)
    {
        return is_numeric($element)
            && ($element === (int) floor($element))
            && ($element === (int) ceil($element))
            && ($element >= $this->lowerLimit)
            && ($element <= $this->upperLimit);
    }
}
