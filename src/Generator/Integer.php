<?php
namespace Generator;

class Integer
{
    public function __construct()
    {
        $this->lowerLimit = 1;
        $this->upperLimit = 1000;
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
