<?php
namespace Eris\Generator;

class SuchThatGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->size = 10;
    }
    
    public function testGeneratesAGeneratedValueObject()
    {
        $generator = new SuchThatGenerator(
            function($n) { return $n % 2 == 0; },
            ConstantGenerator::box(10)
        );
        $this->assertEquals(
            10,
            $generator->__invoke($this->size)->unbox()
        );
    }

    public function testShrinksTheOriginalInput()
    {
        $generator = new SuchThatGenerator(
            function($n) { return $n % 2 == 0; },
            new ChooseGenerator(0, 100)
        );
        $element = $generator->__invoke($this->size);
        for ($i = 0; $i < 100; $i++) {
            $element = $generator->shrink($element);
            $this->assertTrue(
                $generator->contains($element),
                "Every shrunk element should still be contained: " . var_export($element, true)
            );
            $this->assertTrue(
                $element->unbox() % 2 == 0,
                "Element should still be filtered while shrinking: " . var_export($element, true)
            );
        }
    }

}
