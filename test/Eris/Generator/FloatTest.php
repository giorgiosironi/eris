<?php
namespace Eris\Generator;

class FloatTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->size = 300;
    }

    public function testUniformelyPicksFloatNumbersInAnInterval()
    {
        $generator = new Float();
        $sum = 0;
        $trials = 500;
        for ($i = 0; $i < $trials; $i++) {
            $value = $generator($this->size);
            $this->assertInternalType('float', $value);
            $sum += $value;
        }
        $this->assertGreaterThanOrEqual(2, $sum / $trials);
        $this->assertLessThan(8, $sum / $trials);
    }

    public function testShrinksLinearly()
    {
        $generator = new Float();
        $this->assertSame(3.5, $generator->shrink(4.5));
        $this->assertSame(-2.5, $generator->shrink(-3.5));
    }

    public function testWhenBothSignsArePossibleCannotShrinkBelowZero()
    {
        $generator = new Float();
        $this->assertSame(0.0, $generator->shrink(0.0));
        $this->assertSame(0.0, $generator->shrink(0.5));
        $this->assertSame(0.0, $generator->shrink(-0.5));
    }

    /**
     * @expectedException DomainException
     */
    public function testExceptionWhenTryingToShrinkValuesOutsideOfTheDomain()
    {
        $generator = new Float(100.12, 200.12);
        $generator->shrink(300);
    }
}
