<?php

class ListConcatenationTest extends \PHPUnit_Framework_TestCase
{
    public function testLengthIsConserved()
    {
        $first = call_user_func($this->genVector(function() { return rand(); }));
        $second = call_user_func($this->genVector(function() { return rand(); }));
        $this->assertEquals(
            count($first) + count($second),
            count(array_merge($first, $second))
        );
    }

    private function genVector($elementGenerator)
    {
        return function() use ($elementGenerator) {
            $vector = [];
            for ($i = 0; $i < rand(); $i++) {
                $vector[] = $elementGenerator();
            }
            return $vector;
        };
    } 
}
