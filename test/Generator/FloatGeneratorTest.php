<?php
namespace Eris\Generator;

use Eris\PHPUnitDeprecationHelper;
use Eris\Random\RandomRange;
use Eris\Random\RandSource;

class FloatGeneratorTest extends \PHPUnit\Framework\TestCase
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
        $this->size = 300;
        $this->rand = new RandomRange(new RandSource());
    }

    public function testPicksUniformelyPositiveAndNegativeFloatNumbers(): void
    {
        $generator = new FloatGenerator();
        $sum = 0;
        $trials = 500;
        for ($i = 0; $i < $trials; $i++) {
            $value = $generator($this->size, $this->rand);
            PHPUnitDeprecationHelper::assertIsFloat($value->unbox());
            $sum += $value->unbox();
        }
        $mean = $sum / $trials;
        // over a 300 size
        $this->assertLessThan(10, abs($mean));
    }

    public function testShrinksLinearly(): void
    {
        $generator = new FloatGenerator();
        $this->assertSame(3.5, $generator->shrink(GeneratedValueSingle::fromJustValue(4.5))->unbox());
        $this->assertSame(-2.5, $generator->shrink(GeneratedValueSingle::fromJustValue(-3.5))->unbox());
    }

    public function testWhenBothSignsArePossibleCannotShrinkBelowZero(): void
    {
        $generator = new FloatGenerator();
        $this->assertSame(0.0, $generator->shrink(GeneratedValueSingle::fromJustValue(0.0))->unbox());
        $this->assertSame(0.0, $generator->shrink(GeneratedValueSingle::fromJustValue(0.5))->unbox());
        $this->assertSame(0.0, $generator->shrink(GeneratedValueSingle::fromJustValue(-0.5))->unbox());
    }
}
