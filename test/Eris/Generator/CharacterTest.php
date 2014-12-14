<?php
namespace Eris\Generator;

class CharacterTest extends \PHPUnit_Framework_TestCase
{
    public function testBasicAsciiCharacters()
    {
        $generator = Character::ascii();
        for ($i = 0; $i < 100; $i++) {
            $value = $generator();
            $this->assertEquals(1, strlen($value));
            $this->assertGreaterThanOrEqual(0, ord($value));
            $this->assertLessThanOrEqual(127, ord($value));
            $this->assertTrue($generator->contains($value));
        }
    }

    public function testPrintableAsciiCharacters()
    {
        $generator = Character::printableAscii();
        for ($i = 0; $i < 100; $i++) {
            $value = $generator();
            $this->assertEquals(1, strlen($value));
            $this->assertGreaterThanOrEqual(32, ord($value));
            $this->assertLessThanOrEqual(127, ord($value));
            $this->assertTrue($generator->contains($value));
        }
    }

    public function testCharactersCannotShrink()
    {
        $generator = Character::ascii(); 
        $this->assertEquals('A', $generator->shrink('A'));
    }

    public function testContainsOnlyTheSpecifiedRange()
    {
        $generator = Character::ascii(); 
        $this->assertTrue($generator->contains("\0"));
        $this->assertTrue($generator->contains("A"));
        $this->assertTrue($generator->contains("b"));
        $this->assertFalse($generator->contains("é"));
    }
}
