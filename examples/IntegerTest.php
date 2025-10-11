<?php

use Eris\Generators;
use Eris\TestTrait;

class IntegerTest extends \PHPUnit\Framework\TestCase
{
    use TestTrait;

    public function testSumIsCommutative(): void
    {
        $this->forAll(
            Generators::int(),
            Generators::int()
        )
            ->then(function ($first, $second): void {
                $x = $first + $second;
                $y = $second + $first;
                $this->assertEquals(
                    $x,
                    $y,
                    "Sum between {$first} and {$second} should be commutative"
                );
            });
    }

    public function testSumIsAssociative(): void
    {
        $this->forAll(
            Generators::int(),
            Generators::neg(),
            Generators::pos()
        )
            ->then(function ($first, $second, $third): void {
                $x = $first + ($second + $third);
                $y = ($first + $second) + $third;
                $this->assertEquals(
                    $x,
                    $y,
                    "Sum between {$first} and {$second} should be associative"
                );
            });
    }

    public function testByteData(): void
    {
        $this->forAll(
            Generators::byte()
        )
            ->then(function ($byte): void {
                $this->assertTrue(
                    $byte >= 0 && $byte <= 255,
                    "$byte is not a valid value for a byte"
                );
            });
    }
}
