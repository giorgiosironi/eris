<?php
namespace Eris\Generator;

class ChooseGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->size = 0; // ignored by this kind of generator
    }

    public function testPicksRandomlyAnIntegerAmongBoundaries()
    {
        $generator = new ChooseGenerator(-10, 10);
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
        $generator = new ChooseGenerator(-10, 200);
        $value = $generator($this->size);
        $target = 10;
        $distance = abs($target - $value->unbox());
        for ($i = 0; $i < 190; $i++) {
            $newValue = $generator->shrink($value);
            $newDistance = abs($target - $newValue->unbox());
            $this->assertTrue(
                $newDistance <= $distance,
                "Failed asserting that {$newDistance} is less than or equal to {$distance}"
            );
            $value = $newValue;
            $distance = $newDistance;
        }
        $this->assertSame($target, $value->unbox());
    }

    public function testUniformity()
    {
        $generator = new ChooseGenerator(-10, 10000);
        $values = [];
        for ($i = 0; $i < 50; $i++) {
            $values[] = $generator($this->size);
        }
        $this->assertGreaterThan(
            40,
            count(array_filter($values, function($n) { return $n->unbox() > 0; })),
            "The positive numbers should be a vast majority given the interval [-10, 10000]"
        );
    }

    public function testShrinkingStopsToZero()
    {
        $generator = new ChooseGenerator($lowerLimit = 0, $upperLimit = 0);
        $lastValue = $generator($this->size);
        $this->assertSame(0, $generator->shrink($lastValue)->unbox());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testExceptionWhenDomainBoundariesAreNotIntegers()
    {
        $generator = new ChooseGenerator("zero", "twenty");
    }

    public function testCanGenerateSingleInteger()
    {
        $generator = new ChooseGenerator(42, 42);
        $this->assertSame(42, $generator($this->size)->unbox());
        $this->assertSame(42, $generator->shrink($generator($this->size))->unbox());
    }

    /**
     * @expectedException DomainException
     */
    public function testExceptionWhenTryingToShrinkValuesOutsideOfTheDomain()
    {
        $generator = new ChooseGenerator(100, 200);
        $generator->shrink(GeneratedValue::fromJustValue(300));
    }

    public function testTheOrderOfBoundariesDoesNotMatter()
    {
        $this->assertEquals(
            new ChooseGenerator(100, -100),
            new ChooseGenerator(-100, 100)
        );
    }
}
