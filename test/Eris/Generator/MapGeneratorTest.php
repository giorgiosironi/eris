<?php
namespace Eris\Generator;

class MapGeneratorTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->size = 10;
    }
    
    public function testGeneratesAGeneratedValueObject()
    {
        $generator = new MapGenerator(
            function($n) { return $n * 2; },
            ConstantGenerator::box(1)
        );
        $this->assertEquals(
            2,
            $generator->__invoke($this->size)->unbox()
        );
    }

    public function testShrinksTheOriginalInput()
    {
        $generator = new MapGenerator(
            function($n) { return $n * 2; },
            new ChooseGenerator(1, 100)
        );
        $element = $generator->__invoke($this->size);
        $elementAfterShrink = $generator->shrink($element);
        $this->assertTrue(
            $elementAfterShrink->unbox() <= $element->unbox(),
            "Element should have diminished in size"
        );
    }

    public function testChecksTheContainmentOfTheOriginalInput()
    {
        $generator = new MapGenerator(
            function($n) { return $n * 2; },
            new ChooseGenerator(1, 100)
        );
        $element = $generator->__invoke($this->size);
        $this->assertTrue(
            $generator->contains($element),
            "Generator does not contain the element $element, which has previously generated"
        );
    }
}
