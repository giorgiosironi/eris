<?php

use Eris\Generators;
use Eris\TestTrait;

class ChooseTest extends \PHPUnit\Framework\TestCase
{
    use TestTrait;

    public function testSumOfTwoIntegersFromBoundedRangesIsCommutative()
    {
        $this->forAll(
            Generators::choose(-1000, 430),
            Generators::choose(230, -30000)
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
