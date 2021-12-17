<?php
namespace Eris\Generator;

use Eris\Random\RandomRange;
use Eris\Random\RandSource;

class ChooseGeneratorTest extends \PHPUnit\Framework\TestCase
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
        $this->size = 0; // ignored by this kind of generator
        $this->rand = new RandomRange(new RandSource());
    }

    public function testPicksRandomlyAnIntegerAmongBoundaries(): void
    {
        $generator = new ChooseGenerator(-10, 10);
        for ($i = 0; $i < 100; $i++) {
            $value = $generator($this->size, $this->rand)->unbox();
            \Eris\PHPUnitDeprecationHelper::assertIsInt($value);
            $this->assertGreaterThanOrEqual(-10, $value);
            $this->assertLessThanOrEqual(10, $value);
        }
    }

    public function testShrinksLinearlyTowardsTheSmallerAbsoluteValue(): void
    {
        /* Not a good shrinking policy, it should start to shrink from 0 and move
         * towards the smaller absolute value.
         * To be refactored next.
         */
        $generator = new ChooseGenerator(-10, 200);
        $value = $generator($this->size, $this->rand);
        $target = 10;
        $distance = abs($target - $value->unbox());
        for ($i = 0; $i < 190; $i++) {
            $newValue = $generator->shrink($value);
            $newDistance = abs($target - $newValue->unbox());
            $this->assertTrue(
                $newDistance <= $distance,
                "Failed asserting that {$newDistance} is less than or equal to {$distance}"
            );
            $value = $newValue;
            $distance = $newDistance;
        }
        $this->assertSame($target, $value->unbox());
    }

    public function testUniformity(): void
    {
        $generator = new ChooseGenerator(-10, 10000);
        $values = [];
        for ($i = 0; $i < 50; $i++) {
            $values[] = $generator($this->size, $this->rand);
        }
        $this->assertGreaterThan(
            40,
            count(array_filter($values, function ($n) {
                return $n->unbox() > 0;
            })),
            "The positive numbers should be a vast majority given the interval [-10, 10000]"
        );
    }

    public function testShrinkingStopsToZero(): void
    {
        $generator = new ChooseGenerator($lowerLimit = 0, $upperLimit = 0);
        $lastValue = $generator($this->size, $this->rand);
        $this->assertSame(0, $generator->shrink($lastValue)->unbox());
    }

    public function testExceptionWhenDomainBoundariesAreNotIntegers(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $generator = new ChooseGenerator("zero", "twenty");
    }

    public function testCanGenerateSingleInteger(): void
    {
        $generator = new ChooseGenerator(42, 42);
        $this->assertSame(42, $generator($this->size, $this->rand)->unbox());
        $this->assertSame(42, $generator->shrink($generator($this->size, $this->rand))->unbox());
    }

    public function testTheOrderOfBoundariesDoesNotMatter(): void
    {
        $this->assertEquals(
            new ChooseGenerator(100, -100),
            new ChooseGenerator(-100, 100)
        );
    }
}
