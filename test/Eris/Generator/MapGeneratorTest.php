<?php
namespace Eris\Generator;

class MapGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->size = 10;
    }
    
    public function testGeneratesAGeneratedValueObjectKeepingTrackOfTheInputUsed()
    {
        $generator = new MapGenerator(
            function($n) { return $n * 2; },
            ConstantGenerator::box(1)
        );
        $this->assertEquals(
            GeneratedValue::fromValueAndInput(
                2,
                1
            ),
            $generator->__invoke($this->size)
        );
        
    }
}
