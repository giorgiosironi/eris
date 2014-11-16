<?php
namespace Eris\Generator;
use Eris\Generator;

/**
 * Note: for implementation simplicity, right now this is just
 * a PositiveInteger generator.
 */
class Integer implements Generator
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
}
