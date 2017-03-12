<?php
namespace Eris\Random;

class RandomRangeTest extends \PHPUnit_Framework_TestCase
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
}
