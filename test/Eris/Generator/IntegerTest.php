<?php
namespace Eris\Generator;

class IntegerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->size = 10;
    }

    public function testPicksRandomlyAnInteger()
    {
        $generator = new Integer();
        for ($i = 0; $i < 100; $i++) {
            $this->assertTrue($generator->contains($generator($this->size)));
        }
    }

    public function testShrinksLinearlyTowardsZero()
    {
        /* Not a good shrinking policy, it should start to shrink from 0 and move
         * towards the upper size limit.
         * To be fixed in the next weeks.
         */
        $generator = new Integer();
        $value = $generator($this->size);
        for ($i = 0; $i < 20; $i++) {
            $newValue = $generator->shrink($value);
            $this->assertTrue(in_array(abs($value - $newValue), [0, 1]));
            $value = $newValue;
        }
        $this->assertSame(0, $value);
    }

    public function testUniformity()
    {
        $generator = new Integer();
        $values = [];
        for ($i = 0; $i < 1000; $i++) {
            $values[] = $generator($this->size);
        }
        $this->assertGreaterThan(
            400,
            count(array_filter($values, function($n) { return $n > 0; })),
            "The positive numbers should be a vast majority given the interval [-10, 10000]"
        );
    }

    public function testCannotShrinkStopsToZero()
    {
        $generator = new Integer();
        $lastValue = $generator($size = 0);
        $this->assertSame(0, $generator->shrink($lastValue));
    }
}
