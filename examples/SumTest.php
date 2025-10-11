<?php

use Eris\Generators;

function my_sum($first, $second): float|int|array
{
    if ($first >= 42) {
        return $first + $second + 1;
    }
    return $first + $second;
}

class SumTest extends \PHPUnit\Framework\TestCase
{
    use Eris\TestTrait;

    public function testRightIdentityElement(): void
    {
        $this->forAll(
            Generators::nat()
        )
            ->then(function ($number): void {
                $this->assertEquals(
                    $number,
                    my_sum($number, 0),
                    "Summing $number to 0"
                );
            });
    }

    public function testLeftIdentityElement(): void
    {
        $this->forAll(
            Generators::nat()
        )
            ->then(function ($number): void {
                $this->assertEquals(
                    $number,
                    my_sum(0, $number),
                    "Summing 0 to $number"
                );
            });
    }

    public function testEqualToReferencePhpImplementation(): void
    {
        $this->forAll(
            Generators::nat(),
            Generators::nat()
        )
            ->then(function ($first, $second): void {
                $this->assertEquals(
                    $first + $second,
                    my_sum($first, $second),
                    "Summing $first and $second"
                );
            });
    }

    public function testPropertyNeverSatisfied(): void
    {
        $this->forAll(
            Generators::nat(),
            Generators::nat()
        )
            ->then(function ($first, $second): void {
                $this->assertEquals(
                    -1,
                    my_sum($first, $second),
                    "Summing $first and $second"
                );
            });
    }
}
