<?php
namespace Eris\Generator;

class BooleanTest extends \PHPUnit_Framework_TestCase
{
    public function testRandomlyPicksTrueOrFalse()
    {
        $generator = new Boolean();
        for ($i = 0; $i < 10; $i++) {
            $generatedValue = $generator();
            $this->assertTrue($generator->contains($generatedValue));
        }
    }

    public function testShrinksToFalse()
    {
        $generator = new Boolean();
        for ($i = 0; $i < 10; $i++) {
            $generatedValue = $generator();
            $this->assertFalse($generator->shrink($generatedValue));
        }
    }
}
