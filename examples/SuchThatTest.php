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

    public function testSuchThatWithAnAnonymousFunctionForMultipleArguments()
    {
        $this->forAll([
            $this->genNat(),
            $this->genNat(),
        ])
            ->suchThat(function($first, $second) {
                return $first > 42 && $second > 23;
            })        
            ->__invoke(function($first, $second) {
                $this->assertTrue(
                    $first + $second > 42 + 23,
                    "\$first and \$second were filtered to be more than 42 and 23, but they are $first and $second"
                );
            });
    }

    public function testSuchThatWithOnePHPUnitConstraint()
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

    public function testSuchThatWithMultiplePHPUnitConstraints()
    {
        $this->forAll([
            $this->genNat(),
            $this->genNat(),
        ])
            ->suchThat($this->greaterThan(42), $this->greaterThan(23))
            ->__invoke(function($first, $second) {
                $this->assertTrue(
                    $first + $second > 42 + 23,
                    "\$first and \$second were filtered to be more than 42 and 23, but they are $first and $second"
                );
            });
    }

    public function testSuchThatWhichSkipsTooManyValues()
    {
        $this->forAll([
            $this->genNat(),
        ])
            ->suchThat($this->greaterThan(800))
            ->__invoke(function($number) {
                $this->assertTrue(
                    $number > 800
                );
            });
    }
}
