<?php
namespace Eris\Generator;

class ConstantGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->size = 0;
    }

    public function testPicksAlwaysTheValue()
    {
        $generator = new ConstantGenerator(true);
        for ($i = 0; $i < 50; $i++) {
            $this->assertTrue($generator($this->size));
        }
    }

    public function testShrinkAlwaysToTheValue()
    {
        $generator = new ConstantGenerator(true);
        $element = $generator($this->size);
        for ($i = 0; $i < 50; $i++) {
            $this->assertTrue($generator->shrink($element));
        }
    }

    public function testContainsOnlyTheValue()
    {
        $generator = new ConstantGenerator(true);
        $this->assertTrue($generator->contains(true));
        $this->assertFalse($generator->contains(42));
    }

    /**
     * @expectedException DomainException
     */
    public function testShrinkOnlyAcceptsElementsOfTheDomainAsParameters()
    {
        $generator = new ConstantGenerator(5);
        $generator->shrink(10);
    }
}
