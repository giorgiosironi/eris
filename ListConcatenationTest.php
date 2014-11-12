<?php
function my_array_merge($first, $second)
{
    $result = array_merge($first, $second);
    
    // bug: if $result has more than 5 elements...
    if (count($result) >= 5) {
        $result[] = 'Oops!';
    }
    return $result;
}

class ListConcatenationTest extends \PHPUnit_Framework_TestCase
{
    private $iterations = 100;

    public function testLengthIsConserved()
    {
        $this->forAll([
            'first' => $this->genVector($this->genInt()),
            'second' => $this->genVector($this->genInt()),
        ])
            ->__invoke(function($first, $second) {
                $this->assertEquals(
                    count($first) + count($second),
                    count(my_array_merge($first, $second))
                );
            });
    }

    public function testLeftIdentityElement()
    {
        $this->forAll([
            'any' => $this->genVector($this->genInt()),
        ])
            ->__invoke(function($any) {
                $this->assertEquals(
                    $any,
                    my_array_merge([], $any)
                );
            });
    }

    public function testRightIdentityElement()
    {
        $this->forAll([
            'any' => $this->genVector($this->genInt()),
        ])
            ->__invoke(function($any) {
                $this->assertEquals(
                    $any,
                    my_array_merge($any, [])
                );
            });
    }

    private function forAll($generators)
    {
        return new Quantifier\ForAll($generators, $this->iterations, $this);
    }

    private function genVector($elementGenerator)
    {
        return new Generator\Vector($elementGenerator);
    } 

    private function genInt()
    {
        return function() { return rand(); };
    }
}
