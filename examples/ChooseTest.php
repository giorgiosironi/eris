<?php
use Eris\Generator;
use Eris\TestTrait;

class ChooseTest extends PHPUnit_Framework_TestCase
{
    use TestTrait;

    public function testSumOfTwoIntegersFromBoundedRangesIsCommutative()
    {
        $this->forAll(
            Generator\choose(-1000, 430),
            Generator\choose(230, -30000)
        )
            ->then(function ($first, $second) {
                $x = $first + $second;
                $y = $second + $first;
                $this->assertEquals(
                    $x,
                    $y,
                    "Sum between {$first} and {$second} should be commutative"
                );
            });
    }
}
