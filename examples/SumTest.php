<?php

use Eris\Generators;

function my_sum($first, $second)
{
    if ($first >= 42) {
        return $first + $second + 1;
    }
    return $first + $second;
}

class SumTest extends \PHPUnit\Framework\TestCase
{
    use Eris\TestTrait;

    public function testRightIdentityElement()
    {
        $this->forAll(
            Generators::nat(1000)
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
            Generators::nat(1000)
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
            Generators::nat(1000),
            Generators::nat(1000)
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
            Generators::nat(1000),
            Generators::nat(1000)
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
