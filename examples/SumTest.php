<?php
use Eris\Generator;

function my_sum($first, $second)
{
    if ($first >= 42) {
        return $first + $second + 1;
    }
    return $first + $second;
}

class SumTest extends PHPUnit_Framework_TestCase
{
    use Eris\TestTrait;

    public function testRightIdentityElement()
    {
        $this->forAll(
            Generator\nat(1000)
        )
            ->then(function ($number) {
                $this->assertEquals(
                    $number,
                    my_sum($number, 0),
                    "Summing $number to 0"
                );
            });
    }

    public function testLeftIdentityElement()
    {
        $this->forAll(
            Generator\nat(1000)
        )
            ->then(function ($number) {
                $this->assertEquals(
                    $number,
                    my_sum(0, $number),
                    "Summing 0 to $number"
                );
            });
    }

    public function testEqualToReferencePhpImplementation()
    {
        $this->forAll(
            Generator\nat(1000),
            Generator\nat(1000)
        )
            ->then(function ($first, $second) {
                $this->assertEquals(
                    $first + $second,
                    my_sum($first, $second),
                    "Summing $first and $second"
                );
            });
    }

    public function testPropertyNeverSatisfied()
    {
        $this->forAll(
            Generator\nat(1000),
            Generator\nat(1000)
        )
            ->then(function ($first, $second) {
                $this->assertEquals(
                    -1,
                    my_sum($first, $second),
                    "Summing $first and $second"
                );
            });
    }
}
