<?php
namespace Eris\Generator;

class BindGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->size = 10;
    }
    
    public function testGeneratesAGeneratedValueObject()
    {
        $generator = new BindGenerator(
            // TODO: order of parameters should be consistent with map, or not?
            ConstantGenerator::box(4),
            function($n) {
                return new ChooseGenerator($n, $n+10);
            }
        );
        $this->assertInternalType(
            'integer',
            $generator->__invoke($this->size)->unbox()
        );
    }

    public function testShrinksTheOuterGenerator()
    {
        $generator = new BindGenerator(
            new ChooseGenerator(0, 5),
            function($n) {
                return new ChooseGenerator($n, $n + 10);
            }
        );
        $value = $generator->__invoke($this->size);
        for ($i = 0; $i < 20; $i++) {
            $this->assertInternalType(
                'integer',
                $value->unbox()
            );
            $this->assertTrue($generator->contains($value), "Element $value should be contained in the Generator");
            $value = $generator->shrink($value);
        }
        $this->assertLessThanOrEqual(5, $value->unbox());
    }
}
