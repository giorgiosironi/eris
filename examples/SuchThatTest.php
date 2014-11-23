<?php

class SuchThatTest extends \PHPUnit_Framework_TestCase
{
    use Eris\TestTrait;

    public function testSuchThatWithAnAnonymousFunctionForASingleArgument()
    {
        $this->forAll([
            $this->genNat(),
        ])
            ->suchThat(function($n) {
                return $n > 42;
            })        
            ->__invoke(function($number) {
                $this->assertTrue(
                    $number > 42,
                    "\$number was filtered to be more than 42, but it's $number"
                );
            });
    }

    public function testSuchThatWithPHPUnitMatchers()
    {
        $this->forAll([
            $this->genNat(),
        ])
            ->suchThat($this->greaterThan(42))
            ->__invoke(function($number) {
                $this->assertTrue(
                    $number > 42,
                    "\$number was filtered to be more than 42, but it's $number"
                );
            });
    }
}
