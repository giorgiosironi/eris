<?php
namespace Eris\Generator;

class BooleanGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testRandomlyPicksTrueOrFalse()
    {
        $generator = new BooleanGenerator();
        for ($i = 0; $i < 10; $i++) {
            $generatedValue = $generator($_size = 0);
            $this->assertTrue($generator->contains($generatedValue));
        }
    }

    public function testShrinksToFalse()
    {
        $generator = new BooleanGenerator();
        for ($i = 0; $i < 10; $i++) {
            $generatedValue = $generator($_size = 10);
            $this->assertFalse($generator->shrink($generatedValue));
        }
    }

    /**
     * @expectedException DomainException
     */
    public function testShrinkOnlyAcceptsElementsOfTheDomainAsParameters()
    {
        $generator = new BooleanGenerator();
        $generator->shrink(GeneratedValue::fromJustValue(10));
    }
}
