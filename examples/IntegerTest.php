<?php
use Eris\Generator;
use Eris\TestTrait;

class IntegerTest extends PHPUnit_Framework_TestCase
{
    use TestTrait;

    public function testSumIsCommutative()
    {
        $this->forAll(
            Generator\int(),
            Generator\int()
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

    public function testSumIsAssociative()
    {
        $this->forAll(
            Generator\int(),
            Generator\neg(),
            Generator\pos()
        )
            ->then(function ($first, $second, $third) {
                $x = $first + ($second + $third);
                $y = ($first + $second) + $third;
                $this->assertEquals(
                    $x,
                    $y,
                    "Sum between {$first} and {$second} should be associative"
                );
            });
    }

    public function testByteData()
    {
        $this->forAll(
            Generator\byte()
        )
            ->then(function ($byte) {
                $this->assertTrue(
                    $byte >= 0 && $byte <= 255,
                    "$byte is not a valid value for a byte"
                );
            });
    }
}
