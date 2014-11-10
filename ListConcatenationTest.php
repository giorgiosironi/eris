<?php

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
                    count(array_merge($first, $second))
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
                    array_merge([], $any)
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
                    array_merge($any, [])
                );
            });
    }

    private function forAll($generators)
    {
        $this->generators = $generators;
        return $this;
    }

    public function __invoke($assertion)
    {
        for ($i = 0; $i < $this->iterations; $i++) {
            $values = [];
            foreach ($this->generators as $name => $generator) {
                $values[] = $generator();
            }
            call_user_func_array(
                $assertion, $values
            );
        }
    }

    private function genVector($elementGenerator)
    {
        require_once __DIR__ . '/src/Generator/Vector.php';
        return new Generator\Vector($elementGenerator);
    } 

    private function genInt()
    {
        return function() { return rand(); };
    }
}
