<?php
namespace Eris\Random;

use PHPUnit\Framework\Attributes\DataProvider;

class MersenneTwisterTest extends \PHPUnit\Framework\TestCase
{
    public function setUp(): void
    {
        if (defined('HHVM_VERSION')) {
            $this->markTestSkipped('MersenneTwister class does not support HHVM');
        }
    }

    public static function sequences()
    {
        return [
            [424242, 100],
            [0, 100],
            [0xffffffff, 100],
            [0xfffffffffffffff, 100],
        ];
    }

    #[DataProvider('sequences')]
    public function testGeneratesTheSameSequenceAsThePythonOracle($seed, $sample)
    {
        $twister = new MersenneTwister();
        $twister->seed($seed);
        $numbers = [];
        for ($i = 0; $i < $sample; $i++) {
            $numbers[$i] = $twister->extractNumber();
        }
        $oracle = "python " . __DIR__ . "/mt.py $seed $sample";
        exec($oracle, $oracleOutput, $returnCode);
        $this->assertEquals(0, $returnCode);
        $this->assertEquals($oracleOutput, $numbers);
    }

    public function testDistribution()
    {
        $twister = new MersenneTwister();
        $twister->seed(424242);
        $bins = array_fill(0, 2, 0);
        for ($i = 0; $i < 1000; $i++) {
            $number = $twister->extractNumber();
            $bin = (int) floor($number / pow(2, 31));
            $bins[$bin]++;
        }
        foreach ($bins as $count) {
            $this->assertGreaterThan(400, $count);
        }
    }
}
