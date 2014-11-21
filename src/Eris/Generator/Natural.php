<?php
namespace Eris\Generator;
use Eris\Generator;

class Natural implements Generator
{
    public function __construct($lowerLimit = 1, $upperLimit = 1000)
    {
        $this->lowerLimit = $lowerLimit;
        $this->upperLimit = $upperLimit;
    }

    public function __invoke()
    {
        return rand($this->lowerLimit, $this->upperLimit);
    }

    public function shrink($element)
    {
        if ($element > 0) {
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
