<?php
namespace Eris\Generator;

class NaturalTest extends \PHPUnit_Framework_TestCase
{
    public function testPicksNumbersFromAUniformDistribution()
    {
        $generator = new Natural(1, $upperLimit = 10);
        $buckets = [];
        for ($i = 0; $i < 10000; $i++) {
            $value = $generator();
            if (!isset($buckets[$value])) {
                $buckets[$value] = 0;
            }
            $buckets[$value]++;
        }
        $this->assertEquals($upperLimit, count($buckets));
        foreach ($buckets as $bucketValue) {
            // not a statistically correct bound,
            // but should fail very rarely
            $this->assertGreaterThan(800, $bucketValue);
            $this->assertLessThan(1200, $bucketValue);
        }
    }

    public function testShrinksLinearly()
    {
        $generator = new Natural(1, $upperLimit = 1000);
        $lastValue = $generator();
        $this->assertEquals($lastValue - 1, $generator->shrink());
        $this->assertEquals($lastValue - 2, $generator->shrink());
    }

    public function testCannotShrinkBelowZero()
    {
        $generator = new Natural($lowerLimit = 1, $upperLimit = 1);
        $lastValue = $generator();
        $this->assertEquals(0, $generator->shrink());
        $this->assertEquals(0, $generator->shrink());
    }
}
