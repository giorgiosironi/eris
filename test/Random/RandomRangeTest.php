<?php
namespace Eris\Random;

use PHPUnit\Framework\Attributes\DataProvider;
use RuntimeException;

class RandomRangeTest extends \PHPUnit\Framework\TestCase
{
    public function testTheRange()
    {
        if (defined('HHVM_VERSION')) {
            $this->markTestSkipped('MersenneTwister class does not support HHVM');
        }

        $range = new RandomRange(new MersenneTwister());
        $range->seed(424242);
        $bins = [];
        $lower = 10;
        $upper = 20;
        for ($i = 0; $i < 1000; $i++) {
            $number = $range->rand($lower, $upper);
            if (!array_key_exists($number, $bins)) {
                $bins[$number] = 0;
            }
            $bins[$number]++;
        }
        $this->assertEquals(11, count($bins));
        $this->assertEquals(10, min(array_keys($bins)));
        $this->assertEquals(20, max(array_keys($bins)));
        foreach ($bins as $bin) {
            $this->assertGreaterThan(80, $bin);
        }
    }

    public static function wideRanges()
    {
        $ranges = [
            '[0, 2^31]' => [0, 2 ** 31],
            '[0, 2^40]' => [0, 2 ** 40],
            '[0, PHP_INT_MAX]' => [0, PHP_INT_MAX],
            '[-1, PHP_INT_MAX]' => [-1, PHP_INT_MAX],
            '[-PHP_INT_MAX, PHP_INT_MAX]' => [-PHP_INT_MAX, PHP_INT_MAX],
            '[PHP_INT_MIN, PHP_INT_MAX]' => [PHP_INT_MIN, PHP_INT_MAX],
            '[PHP_INT_MIN, 0]' => [PHP_INT_MIN, 0],
        ];
        $sources = [
            'MtRandSource' => MtRandSource::class,
            'RandSource' => RandSource::class,
            'MersenneTwister' => MersenneTwister::class,
        ];

        $data = [];
        foreach ($sources as $sourceName => $source) {
            foreach ($ranges as $rangeName => [$lower, $upper]) {
                $data["$rangeName with $sourceName"] = [$source, $lower, $upper];
            }
        }

        return $data;
    }

    #[DataProvider('wideRanges')]
    public function testGeneratesUniformlyDistributedIntegersInRangesWiderThanTheSource($source, $lower, $upper)
    {
        $range = new RandomRange(new $source());
        $range->seed(424242);
        $middle = intdiv($lower, 2) + intdiv($upper, 2);
        $odd = 0;
        $belowTheMiddle = 0;
        for ($i = 0; $i < 1000; $i++) {
            $number = $range->rand($lower, $upper);
            $this->assertIsInt($number);
            $this->assertGreaterThanOrEqual($lower, $number);
            $this->assertLessThanOrEqual($upper, $number);
            $odd += $number & 1;
            $belowTheMiddle += $number < $middle ? 1 : 0;
        }
        $this->assertGreaterThan(400, $odd);
        $this->assertLessThan(600, $odd);
        $this->assertGreaterThan(400, $belowTheMiddle);
        $this->assertLessThan(600, $belowTheMiddle);
    }

    public static function rangesThatABrokenSourceCannotHit()
    {
        return [
            'range narrower than the source' => [0, 10],
            'range wider than the source' => [0, 2 ** 32],
            'range wider than PHP_INT_MAX' => [-1, PHP_INT_MAX],
        ];
    }

    #[DataProvider('rangesThatABrokenSourceCannotHit')]
    public function testGivesUpWhenTheSourceDoesNotProduceANumberInsideTheRange($lower, $upper)
    {
        $source = $this->createStub(Source::class);
        $source->method('max')->willReturn(0x7fffffff);
        $source->method('extractNumber')->willReturn(0x7fffffff);
        $range = new RandomRange($source);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Could not generate a random number between $lower and $upper in 100 attempts.");

        $range->rand($lower, $upper);
    }
}
