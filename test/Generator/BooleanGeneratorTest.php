<?php
namespace Eris\Generator;

class BooleanGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->rand = 'rand';
    }
    
    public function testRandomlyPicksTrueOrFalse()
    {
        $generator = new BooleanGenerator();
        for ($i = 0; $i < 10; $i++) {
            $generatedValue = $generator($_size = 0, $this->rand);
            $this->assertInternalType('bool', $generatedValue->unbox());
        }
    }

    public function testShrinksToFalse()
    {
        $generator = new BooleanGenerator();
        for ($i = 0; $i < 10; $i++) {
            $generatedValue = $generator($_size = 10, $this->rand);
            $this->assertFalse($generator->shrink($generatedValue));
        }
    }
}
