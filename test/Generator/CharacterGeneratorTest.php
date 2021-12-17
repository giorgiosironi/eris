<?php
namespace Eris\Generator;

use Eris\Random\RandomRange;
use Eris\Random\RandSource;

class CharacterGeneratorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var int
     */
    private $size;
    /**
     * @var RandomRange
     */
    private $rand;

    protected function setUp(): void
    {
        $this->size = 0;
        $this->rand = new RandomRange(new RandSource());
    }

    public function testBasicAsciiCharacterGenerators(): void
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

    public function testPrintableAsciiCharacterGenerators(): void
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

    public function testCharacterGeneratorsShrinkByConventionToTheLowestCodePoint(): void
    {
        $generator = CharacterGenerator::ascii();
        $this->assertEquals('@', $generator->shrink(GeneratedValueSingle::fromJustValue('A', 'character'))->unbox());
    }

    public function testTheLowestCodePointCannotBeShrunk(): void
    {
        $generator = new CharacterGenerator(65, 90);
        $lowest = GeneratedValueSingle::fromJustValue('A', 'character');
        $this->assertEquals($lowest, $generator->shrink($lowest));
    }
}
