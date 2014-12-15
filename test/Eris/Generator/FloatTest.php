<?php
namespace Eris\Generator;

class FloatTest extends \PHPUnit_Framework_TestCase
{
    public function testUniformelyPicksFloatNumbersInAnInterval()
    {
        $generator = new Float($lowerLimit = -10.0, $upperLimit = 20.0);
        $sum = 0;
        $trials = 500;
        for ($i = 0; $i < $trials; $i++) {
            $value = $generator();
            $this->assertInternalType('float', $value);
            $this->assertGreaterThanOrEqual($lowerLimit, $value);
            $this->assertLessThanOrEqual($upperLimit, $value);
            $sum += $value;
        }
        $this->assertGreaterThanOrEqual(2, $sum / $trials);
        $this->assertLessThan(8, $sum / $trials);
    }

    public function testShrinksLinearly()
    {
        $generator = new Float($lowerLimit = 1, $upperLimit = 1000);
        $this->assertSame(3.5, $generator->shrink(4.5));
        $this->assertSame(-3.5, $generator->shrink(-4.5));
    }

    public function testWhenBothSignsArePossibleCannotShrinkBelowZero()
    {
        $generator = new Float($lowerLimit = -10.0, $upperLimit = 10.0);
        $this->assertSame(0.0, $generator->shrink(0.0));
        $this->assertSame(0.0, $generator->shrink(0.5));
        $this->assertSame(0.0, $generator->shrink(-0.5));
    }

    public function testWhenPositiveCannotShrinkBelowLowerLimit()
    {
        $generator = new Float($lowerLimit = 5.0, $upperLimit = 10.0);
        $this->assertSame(5.0, $generator->shrink(5.5));
    }

    public function testWhenNegativeCannotShrinkOverUpperLimit()
    {
        $generator = new Float($lowerLimit = -10.0, $upperLimit = -5.0);
        $this->assertSame(-5.0, $generator->shrink(-5.5));
    }

    public function testContainsOnlyFloatsInTheInterval()
    {
        $generator = new Float(4.0, 8.0);
        $this->assertTrue($generator->contains(4.0), "4.0 is not contained");
        $this->assertTrue($generator->contains(5.0), "5.0 is not contained");
        $this->assertTrue($generator->contains(8.0), "8.0 is not contained");
        $this->assertFalse($generator->contains(0.0), "0.0 is contained but it should not be");
        $this->assertFalse($generator->contains(9.0), "9.0 is contained but it should not be");
        $this->assertFalse($generator->contains(0), "0 is contained but it should not be");

    }

    public function testNumbersAreCastedAtCreation()
    {
        $this->assertEquals(
            new Float(4.0, 8.0),
            new Float(4, 8)
        );
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testNonNumbersCannotBeUsedForCreation()
    {
        new Float(4, "eight");
    }

    public function testTheOrderOfIntervalBoundariesDoesNotMatter()
    {
        $this->assertEquals(
            new Float(0.0, 1.0),
            new Float(1.0, 0.0)
        );
    }
}
