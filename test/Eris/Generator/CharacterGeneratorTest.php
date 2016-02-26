<?php
namespace Eris\Generator;

class CharacterGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->size = 0;
    }

    public function testBasicAsciiCharacterGenerators()
    {
        $generator = CharacterGenerator::ascii();
        for ($i = 0; $i < 100; $i++) {
            $value = $generator($this->size)->unbox();
            $this->assertEquals(1, strlen($value));
            $this->assertGreaterThanOrEqual(0, ord($value));
            $this->assertLessThanOrEqual(127, ord($value));
            $this->assertTrue($generator->contains($value));
        }
    }

    public function testPrintableAsciiCharacterGenerators()
    {
        $generator = CharacterGenerator::printableAscii();
        for ($i = 0; $i < 100; $i++) {
            $value = $generator($this->size)->unbox();
            $this->assertEquals(1, strlen($value));
            $this->assertGreaterThanOrEqual(32, ord($value));
            $this->assertLessThanOrEqual(127, ord($value));
            $this->assertTrue($generator->contains($value));
        }
    }

    public function testCharacterGeneratorsShrinkByConventionToTheLowestCodePoint()
    {
        $generator = CharacterGenerator::ascii();
        $this->assertEquals('@', $generator->shrink(GeneratedValue::fromJustValue('A', 'character'))->unbox());
    }

    public function testTheLowestCodePointCannotBeShrunk()
    {
        $generator = new CharacterGenerator(65, 90);
        $lowest = GeneratedValue::fromJustValue('A', 'character');
        $this->assertEquals($lowest, $generator->shrink($lowest));
    }

    public function testContainsOnlyTheSpecifiedRange()
    {
        $generator = CharacterGenerator::ascii();
        $this->assertTrue($generator->contains("\0"));
        $this->assertTrue($generator->contains("A"));
        $this->assertTrue($generator->contains("b"));
        $this->assertFalse($generator->contains("é"));
    }
}
