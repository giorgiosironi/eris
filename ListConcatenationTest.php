<?php

class ListConcatenationTest extends \PHPUnit_Framework_TestCase
{
    public function testLengthIsConserved()
    {
        $first = call_user_func($this->genVector($this->genInt()));
        $second = call_user_func($this->genVector($this->genInt()));
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

    private function genInt()
    {
        return function() { return rand(); };
    }
}
