<?php
namespace Eris\Generator;

class IntegerGeneratorTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->size = 10;
        $this->rand = 'rand';
    }

    public function testPicksRandomlyAnInteger()
    {
        $generator = new IntegerGenerator();
        for ($i = 0; $i < 100; $i++) {
            $this->assertInternalType('integer', $generator($this->size, $this->rand)->unbox());
        }
    }

    public function testShrinksLinearlyTowardsZero()
    {
        $generator = new IntegerGenerator();
        $value = $generator($this->size, $this->rand);
        for ($i = 0; $i < 20; $i++) {
            $value = GeneratedValueOptions::mostPessimisticChoice($value);
            $value = $generator->shrink($value);
        }
        $this->assertSame(0, $value->unbox());
    }

    public function testOffersMultiplePossibilitiesForShrinkingProgressivelySubtracting()
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

    public function testUniformity()
    {
        $generator = new IntegerGenerator();
        $values = [];
        for ($i = 0; $i < 1000; $i++) {
            $values[] = $generator($this->size, $this->rand);
        }
        $this->assertGreaterThan(
            400,
            count(array_filter($values, function ($n) {
                return $n->unbox() > 0;
            })),
            "The positive numbers should be a vast majority given the interval [-10, 10000]"
        );
    }

    public function testShrinkingStopsToZero()
    {
        $generator = new IntegerGenerator();
        $lastValue = $generator($size = 0, $this->rand);
        $this->assertSame(0, $generator->shrink($lastValue)->unbox());
    }

    public function testPosAlreadyStartsFromStrictlyPositiveValues()
    {
        $generator = pos();
        $this->assertGreaterThan(0, $generator->__invoke(0, $this->rand)->unbox());
    }

    public function testPosNeverShrinksToZero()
    {
        $generator = pos();
        $value = $generator->__invoke(10, $this->rand);
        for ($i = 0; $i < 20; $i++) {
            $value = $generator->shrink(GeneratedValueOptions::mostPessimisticChoice($value));
            $this->assertNotEquals(0, $value->unbox());
        }
    }

    public function testNegAlreadyStartsFromStrictlyNegativeValues()
    {
        $generator = neg();
        $this->assertLessThan(0, $generator->__invoke(0, $this->rand)->unbox());
    }

    public function testNegNeverShrinksToZero()
    {
        $generator = neg();
        $value = $generator->__invoke(10, $this->rand);
        for ($i = 0; $i < 20; $i++) {
            $value = $generator->shrink(GeneratedValueOptions::mostPessimisticChoice($value));
            $this->assertNotEquals(0, $value->unbox());
        }
    }

    public function testNatStartsFromZero()
    {
        $generator = nat();
        $this->assertEquals(0, $generator->__invoke(0, $this->rand)->unbox());
    }
}
