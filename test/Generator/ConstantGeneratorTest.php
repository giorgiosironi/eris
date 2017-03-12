<?php
namespace Eris\Generator;

class ConstantGeneratorTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->size = 0;
        $this->rand = 'rand';
    }

    public function testPicksAlwaysTheValue()
    {
        $generator = new ConstantGenerator(true);
        for ($i = 0; $i < 50; $i++) {
            $this->assertTrue($generator($this->size, $this->rand)->unbox());
        }
    }

    public function testShrinkAlwaysToTheValue()
    {
        $generator = new ConstantGenerator(true);
        $element = $generator($this->size, $this->rand);
        for ($i = 0; $i < 50; $i++) {
            $this->assertTrue($generator->shrink($element)->unbox());
        }
    }

    public function testContainsOnlyTheValue()
    {
        $generator = new ConstantGenerator(true);
        $this->assertTrue($generator->contains(GeneratedValueSingle::fromJustValue(true)));
        $this->assertFalse($generator->contains(GeneratedValueSingle::fromJustValue(42)));
    }

    /**
     * @expectedException DomainException
     */
    public function testShrinkOnlyAcceptsElementsOfTheDomainAsParameters()
    {
        $generator = new ConstantGenerator(5);
        $generator->shrink(GeneratedValueSingle::fromJustValue(10));
    }
}
