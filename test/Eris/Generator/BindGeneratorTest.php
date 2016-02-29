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
}
