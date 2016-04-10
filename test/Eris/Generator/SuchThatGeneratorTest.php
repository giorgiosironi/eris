<?php
namespace Eris\Generator;

class SuchThatGeneratorTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->size = 10;
        $this->rand = 'rand';
    }
    
    public function testGeneratesAGeneratedValueObject()
    {
        $generator = new SuchThatGenerator(
            function($n) { return $n % 2 == 0; },
            ConstantGenerator::box(10)
        );
        $this->assertSame(
            10,
            $generator->__invoke($this->size, $this->rand)->unbox()
        );
    }

    public function testAcceptsPHPUnitConstraints()
    {
        $generator = new SuchThatGenerator(
            $this->callback(function($n) { return $n % 2 == 0; }),
            ConstantGenerator::box(10)
        );
        $this->assertSame(
            10,
            $generator->__invoke($this->size, $this->rand)->unbox()
        );
    }

    public function testShrinksTheOriginalInput()
    {
        $generator = new SuchThatGenerator(
            function($n) { return $n % 2 == 0; },
            new ChooseGenerator(0, 100)
        );
        $element = $generator->__invoke($this->size, $this->rand);
        for ($i = 0; $i < 100; $i++) {
            $element = $generator->shrink($element);
            $this->assertTrue(
                $generator->contains($element),
                "Every shrunk element should still be contained: " . var_export($element, true)
            );
            $this->assertTrue(
                $element->unbox() % 2 === 0,
                "Element should still be filtered while shrinking: " . var_export($element, true)
            );
        }
    }

    /**
     * @expectedException LogicException
     */
    public function testGivesUpGenerationIfTheFilterIsNotSatisfiedTooManyTimes()
    {
        $generator = new SuchThatGenerator(
            function($n) { return $n % 2 == 0; },
            ConstantGenerator::box(11)
        );
        $generator->__invoke($this->size, $this->rand);
    }

    public function testGivesUpShrinkingIfTheFilterIsNotSatisfiedTooManyTimes()
    {
        $generator = new SuchThatGenerator(
            function($n) { return $n % 250 == 0; },
            new ChooseGenerator(0, 1000)
        );
        $unshrinkable = GeneratedValue::fromJustValue(470);
        $this->assertEquals(
            $unshrinkable,
            $generator->shrink($unshrinkable)
        );
    }
}
