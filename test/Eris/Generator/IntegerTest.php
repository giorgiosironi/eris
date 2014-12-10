<?php
namespace Eris\Generator;

class IntegerTest extends \PHPUnit_Framework_TestCase
{
    public function testPicksRandomlyAnInteger()
    {
        $generator = new Integer();
        for ($i = 0; $i < 100; $i++) {
            $this->assertTrue($generator->contains($generator()));
        }
    }

    public function testShrinksLinearlyTowardsZero()
    {
        $generator = new Integer(-10, 10);
        $value = $generator();
        for ($i = 0; $i < 20; $i++) {
            $newValue = $generator->shrink($value);
            $this->assertTrue(in_array(abs($value - $newValue), [0, 1]));
            $value = $newValue;
        }
        $this->assertEquals(0, $value);
    }

    public function testCannotShrinkStopsToZero()
    {
        $generator = new Integer($lowerLimit = 0, $upperLimit = 0);
        $lastValue = $generator();
        $this->assertEquals(0, $generator->shrink($lastValue));
    }
}
