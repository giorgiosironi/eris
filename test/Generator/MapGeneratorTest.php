<?php
namespace Eris\Generator;

use Eris\Random\RandomRange;
use Eris\Random\RandSource;

class MapGeneratorTest extends \PHPUnit\Framework\TestCase
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
        $this->size = 10;
        $this->rand = new RandomRange(new RandSource());
    }
    
    public function testGeneratesAGeneratedValueObject()
    {
        $generator = new MapGenerator(
            function ($n) {
                return $n * 2;
            },
            ConstantGenerator::box(1)
        );
        $this->assertEquals(
            2,
            $generator->__invoke($this->size, $this->rand)->unbox()
        );
    }

    public function testShrinksTheOriginalInput()
    {
        $generator = new MapGenerator(
            function ($n) {
                return $n * 2;
            },
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
