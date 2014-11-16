<?php
namespace Generator;

class Integer
{
    public function __construct()
    {
        $this->lowerLimit = 1;
        $this->upperLimit = 1000;
    }

    public function __invoke()
    {
        return rand($this->lowerLimit, $this->upperLimit);
    }

    public function shrink()
    {
        $this->upperLimit--;
        return $this;
    }
}
