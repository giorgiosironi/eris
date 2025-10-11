<?php
namespace Eris\Generator;

use Eris\Random\RandomRange;
use Eris\Random\RandSource;

class MapGeneratorTest extends \PHPUnit\Framework\TestCase
{
    private int $size;
    private \Eris\Random\RandomRange $rand;

    protected function setUp(): void
    {
        $this->size = 10;
        $this->rand = new RandomRange(new RandSource());
    }
    
    public function testGeneratesAGeneratedValueObject(): void
    {
        $generator = new MapGenerator(
            fn ($n): int|float => $n * 2,
            ConstantGenerator::box(1)
        );
        $this->assertEquals(
            2,
            $generator->__invoke($this->size, $this->rand)->unbox()
        );
    }

    public function testShrinksTheOriginalInput(): void
    {
        $generator = new MapGenerator(
            fn ($n): int|float => $n * 2,
            new ChooseGenerator(1, 100)
        );
        $element = $generator->__invoke($this->size, $this->rand);
        $elementAfterShrink = $generator->shrink($element);
        $this->assertTrue(
            $elementAfterShrink->unbox() <= $element->unbox(),
            "Element should have diminished in size"
        );
    }
}
