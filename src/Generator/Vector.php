<?php
namespace Generator;

class Vector
{
    private $elementGenerator;
    
    public function __construct($elementGenerator)
    {
        $this->elementGenerator = $elementGenerator;
        $this->lowerLimit = 1;
        $this->upperLimit = 1000;
    }

    public function __invoke()
    {
        $vector = [];
        for ($i = 0; $i < rand($this->lowerLimit, $this->upperLimit); $i++) {
            $vector[] = call_user_func($this->elementGenerator);
        }
        return $vector;
    }

    public function shrink()
    {
        $this->lowerLimit = $this->upperLimit;
        $this->upperLimit--;
        return $this;
    }
}
