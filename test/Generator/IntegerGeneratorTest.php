<?php
namespace Eris\Generator;

use Eris\Random\RandomRange;
use Eris\Random\RandSource;

class IntegerGeneratorTest extends \PHPUnit\Framework\TestCase
{
    private int $size;
    private \Eris\Random\RandomRange $rand;

    protected function setUp(): void
    {
        $this->size = 10;
        $this->rand = new RandomRange(new RandSource());
    }

    public function testPicksRandomlyAnInteger(): void
    {
        $generator = new IntegerGenerator();
        for ($i = 0; $i < 100; $i++) {
            self::assertIsInt($generator($this->size, $this->rand)->unbox());
        }
    }

    public function testShrinksLinearlyTowardsZero(): void
    {
        $generator = new IntegerGenerator();
        $value = $generator($this->size, $this->rand);
        for ($i = 0; $i < 20; $i++) {
            $value = GeneratedValueOptions::mostPessimisticChoice($value);
            $value = $generator->shrink($value);
        }
        $this->assertSame(0, $value->unbox());
    }

    public function testOffersMultiplePossibilitiesForShrinkingProgressivelySubtracting(): void
    {
        $generator = new IntegerGenerator();
        $value = GeneratedValueSingle::fromJustValue(100, 'integer');
        $shrinkingOptions = $generator->shrink($value);
        $this->assertEquals(
            new GeneratedValueOptions([
                GeneratedValueSingle::fromJustValue(50, 'integer'),
                GeneratedValueSingle::fromJustValue(75, 'integer'),
                GeneratedValueSingle::fromJustValue(88, 'integer'),
                GeneratedValueSingle::fromJustValue(94, 'integer'),
                GeneratedValueSingle::fromJustValue(97, 'integer'),
                GeneratedValueSingle::fromJustValue(99, 'integer'),
            ]),
            $shrinkingOptions
        );
    }

    public function testUniformity(): void
    {
        $generator = new IntegerGenerator();
        $values = [];
        for ($i = 0; $i < 1000; $i++) {
            $values[] = $generator($this->size, $this->rand);
        }
        $this->assertGreaterThan(
            400,
            count(array_filter($values, fn($n): bool => $n->unbox() > 0)),
            "The positive numbers should be a vast majority given the interval [-10, 10000]"
        );
    }

    public function testShrinkingStopsToZero(): void
    {
        $generator = new IntegerGenerator();
        $lastValue = $generator($size = 0, $this->rand);
        $this->assertSame(0, $generator->shrink($lastValue)->unbox());
    }

    public function testPosAlreadyStartsFromStrictlyPositiveValues(): void
    {
        $generator = pos();
        $this->assertGreaterThan(0, $generator->__invoke(0, $this->rand)->unbox());
    }

    public function testPosNeverShrinksToZero(): void
    {
        $generator = pos();
        $value = $generator->__invoke(10, $this->rand);
        for ($i = 0; $i < 20; $i++) {
            $value = $generator->shrink(GeneratedValueOptions::mostPessimisticChoice($value));
            $this->assertNotEquals(0, $value->unbox());
        }
    }

    public function testNegAlreadyStartsFromStrictlyNegativeValues(): void
    {
        $generator = neg();
        $this->assertLessThan(0, $generator->__invoke(0, $this->rand)->unbox());
    }

    public function testNegNeverShrinksToZero(): void
    {
        $generator = neg();
        $value = $generator->__invoke(10, $this->rand);
        for ($i = 0; $i < 20; $i++) {
            $value = $generator->shrink(GeneratedValueOptions::mostPessimisticChoice($value));
            $this->assertNotEquals(0, $value->unbox());
        }
    }

    public function testNatStartsFromZero(): void
    {
        $generator = nat();
        $this->assertEquals(0, $generator->__invoke(0, $this->rand)->unbox());
    }
}
