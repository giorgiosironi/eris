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
        $this->assertSame($upperLimit, count($buckets));
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
        $this->assertSame($lastValue - 1, $generator->shrink($lastValue));
    }

    public function testCouldNotShrinkMoreLowerLimit()
    {
        $generator = new Natural(10, 100);
        $this->assertSame(10, $generator->shrink(10));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testExceptionWhenLowerLimitIsLowerThanZero()
    {
        new Natural(-1, 1);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testExceptionWhenLimitsAreNotIntegers()
    {
        new Natural("nine", "twenty");
    }

    /**
     * @expectedException DomainException
     */
    public function testExceptionWhenTryingToShrinkValuesOutsideOfTheDomain()
    {
        $generator = new Natural(2, 4);
        $generator->shrink(5);
    }

    public function testTheOrderOfBoundariesDoesNotMatter()
    {
        $this->assertEquals(
            new Natural(0, 100),
            new Natural(100, 0)
        );
    }
}
