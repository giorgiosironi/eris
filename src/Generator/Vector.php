<?php
namespace Generator;

class Vector
{
    private $elementGenerator;
    
    public function __construct($elementGenerator)
    {
        $this->elementGenerator = $elementGenerator;
        $this->upperLimit = 1000;
    }

    public function __invoke()
    {
        $vector = [];
        for ($i = 0; $i < rand(1, $this->upperLimit); $i++) {
            $vector[] = call_user_func($this->elementGenerator);
        }
        return $vector;
    }

    public function shrink()
    {
        $this->upperLimit--;
        return $this;
    }
}
