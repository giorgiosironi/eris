<?php
namespace Eris\Generator;
use Eris\Generator;

class Natural implements Generator
{
    public function __construct($lowerLimit = 1, $upperLimit = 1000)
    {
        $this->lowerLimit = $lowerLimit;
        $this->upperLimit = $upperLimit;
        $this->lastGenerated = null;
    }

    public function __invoke()
    {
        $this->lastGenerated = rand($this->lowerLimit, $this->upperLimit);
        return $this->lastGenerated;
    }

    public function shrink()
    {
        if ($this->lastGenerated > 0) {
            $this->lastGenerated--;
        }
        return $this->lastGenerated;
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
