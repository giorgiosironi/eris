<?php
namespace Eris\Generator;

class ChooseTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->size = 0; // ignored by this kind of generator
    }

    public function testPicksRandomlyAnIntegerAmongBoundaries()
    {
        $generator = new Choose(-10, 10);
        for ($i = 0; $i < 100; $i++) {
            $this->assertTrue(
                $generator->contains($value = $generator($this->size)),
                "Failed to assert that the value {$value} is between -10 and 10"
            );
        }
    }

    public function testShrinksLinearlyTowardsTheSmallerAbsoluteValue()
    {
        /* Not a good shrinking policy, it should start to shrink from 0 and move
         * towards the smaller absolute value.
         * To be refactored next.
         */
        $generator = new Choose(-10, 200);
        $value = $generator($this->size);
        $target = 10;
        $distance = abs($target - $value);
        for ($i = 0; $i < 190; $i++) {
            $newValue = $generator->shrink($value);
            $newDistance = abs($target - $newValue);
            $this->assertTrue(
                $newDistance <= $distance,
                "Failed asserting that {$newDistance} is less than or equal to {$distance}"
            );
            $value = $newValue;
            $distance = $newDistance;
        }
        $this->assertSame($target, $value);
    }

    public function testUniformity()
    {
        $generator = new Choose(-10, 10000);
        $values = [];
        for ($i = 0; $i < 50; $i++) {
            $values[] = $generator();
        }
        $this->assertGreaterThan(
            40,
            count(array_filter($values, function($n) { return $n > 0; })),
            "The positive numbers should be a vast majority given the interval [-10, 10000]"
        );
    }

    public function testCannotShrinkStopsToZero()
    {
        $generator = new Choose($lowerLimit = 0, $upperLimit = 0);
        $lastValue = $generator($this->size);
        $this->assertSame(0, $generator->shrink($lastValue));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testExceptionWhenDomainBoundariesAreNotIntegers()
    {
        $generator = new Choose("zero", "twenty");
    }

    public function testCanGenerateSingleInteger()
    {
        $generator = new Choose(42, 42);
        $this->assertSame(42, $generator($this->size));
        $this->assertSame(42, $generator->shrink($generator()));
    }

    /**
     * @expectedException DomainException
     */
    public function testExceptionWhenTryingToShrinkValuesOutsideOfTheDomain()
    {
        $generator = new Choose(100, 200);
        $generator->shrink(300);
    }

    public function testTheOrderOfBoundariesDoesNotMatter()
    {
        $this->assertEquals(
            new Choose(100, -100),
            new Choose(-100, 100)
        );
    }
}