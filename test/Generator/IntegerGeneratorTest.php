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
            $this->assertTrue($generator->contains($generator($this->size, $this->rand)));
        }
    }

    public function testShrinksLinearlyTowardsZero()
    {
        /* Not a good shrinking policy, it should start to shrink from 0 and move
         * towards the upper size limit.
         * To be fixed in the next weeks.
         */
        $generator = new IntegerGenerator();
        $value = $generator($this->size, $this->rand);
        for ($i = 0; $i < 20; $i++) {
            $newValue = $generator->shrink($value);
            $this->assertTrue(in_array(abs($value->unbox() - $newValue->unbox()), [0, 1]));
            $value = $newValue;
        }
        $this->assertSame(0, $value->unbox());
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

    public function testCannotShrinkStopsToZero()
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

    public function testNegAlreadyStartsFromStrictlyNegativeValues()
    {
        $generator = neg();
        $this->assertLessThan(0, $generator->__invoke(0, $this->rand)->unbox());
    }

    public function testNatStartsFromZero()
    {
        $generator = nat();
        $this->assertEquals(0, $generator->__invoke(0, $this->rand)->unbox());
    }
}
