<?php
namespace Eris\Generator;

use Eris\Random\RandomRange;
use Eris\Random\RandSource;

class ConstantGeneratorTest extends \PHPUnit\Framework\TestCase
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

    public function testPicksAlwaysTheValue(): void
    {
        $generator = new ConstantGenerator(true);
        for ($i = 0; $i < 50; $i++) {
            $this->assertTrue($generator($this->size, $this->rand)->unbox());
        }
    }

    public function testShrinkAlwaysToTheValue(): void
    {
        $generator = new ConstantGenerator(true);
        $element = $generator($this->size, $this->rand);
        for ($i = 0; $i < 50; $i++) {
            $this->assertTrue($generator->shrink($element)->unbox());
        }
    }
}
