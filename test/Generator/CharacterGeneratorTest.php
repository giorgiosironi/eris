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
}
