<?php
namespace Generator;

class Vector
{
    private $elementGenerator;
    
    public function __construct($elementGenerator)
    {
        $this->elementGenerator = $elementGenerator;
    }

    public function __invoke()
    {
        $vector = [];
        for ($i = 0; $i < rand(1, 10); $i++) {
            $vector[] = call_user_func($this->elementGenerator);
        }
        return $vector;
    }
}
