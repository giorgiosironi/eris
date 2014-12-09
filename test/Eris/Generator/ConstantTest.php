<?php
namespace Eris\Generator;

class ConstantTest extends \PHPUnit_Framework_TestCase
{
    public function testPicksAlwaysTheValue()
    {
        $generator = new Constant(true);
        for ($i = 0; $i < 50; $i++) {
            $this->assertTrue($generator());
        }
    }

    public function testShrinkAlwaysToTheValue()
    {
        $generator = new Constant(true);
        $element = $generator();
        for ($i = 0; $i < 50; $i++) {
            $this->assertTrue($generator->shrink($element));
        }
    }

    public function testContainsOnlyTheValue()
    {
        $generator = new Constant(true);
        $this->assertTrue($generator->contains(true));
        $this->assertFalse($generator->contains(42));
    }
}
