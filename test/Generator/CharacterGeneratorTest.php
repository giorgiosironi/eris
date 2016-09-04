<?php
namespace Eris\Generator;

class CharacterGeneratorTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->size = 0;
        $this->rand = 'rand';
    }

    public function testBasicAsciiCharacterGenerators()
    {
        $generator = CharacterGenerator::ascii();
        for ($i = 0; $i < 100; $i++) {
            $generatedValue = $generator($this->size, $this->rand);
            $value = $generatedValue->unbox();
            $this->assertEquals(1, strlen($value));
            $this->assertGreaterThanOrEqual(0, ord($value));
            $this->assertLessThanOrEqual(127, ord($value));
            $this->assertTrue($generator->contains($generatedValue));
        }
    }

    public function testPrintableAsciiCharacterGenerators()
    {
        $generator = CharacterGenerator::printableAscii();
        for ($i = 0; $i < 100; $i++) {
            $generatedValue = $generator($this->size, $this->rand);
            $value = $generatedValue->unbox();
            $this->assertEquals(1, strlen($value));
            $this->assertGreaterThanOrEqual(32, ord($value));
            $this->assertLessThanOrEqual(127, ord($value));
            $this->assertTrue($generator->contains($generatedValue));
        }
    }

    public function testCharacterGeneratorsShrinkByConventionToTheLowestCodePoint()
    {
        $generator = CharacterGenerator::ascii();
        $this->assertEquals('@', $generator->shrink(GeneratedValueSingle::fromJustValue('A', 'character'))->unbox());
    }

    public function testTheLowestCodePointCannotBeShrunk()
    {
        $generator = new CharacterGenerator(65, 90);
        $lowest = GeneratedValueSingle::fromJustValue('A', 'character');
        $this->assertEquals($lowest, $generator->shrink($lowest));
    }

    public function testContainsOnlyTheSpecifiedRange()
    {
        $generator = CharacterGenerator::ascii();
        $this->assertTrue($generator->contains(GeneratedValueSingle::fromJustValue("\0")));
        $this->assertTrue($generator->contains(GeneratedValueSingle::fromJustValue("A")));
        $this->assertTrue($generator->contains(GeneratedValueSingle::fromJustValue("b")));
        $this->assertFalse($generator->contains(GeneratedValueSingle::fromJustValue("é")));
    }
}
