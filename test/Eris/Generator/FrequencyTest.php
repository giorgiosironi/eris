<?php
namespace Eris\Generator;

class FrequencyTest extends \PHPUnit_Framework_TestCase
{
    public function testMoreFrequentGeneratorIsChosenMoreOften()
    {
        $countOf = [42 => 0, 21 => 0];
        for ($i=0; $i<1000; $i++) {
            $generator = frequency([
                [10, 42],
                [1, 21],
            ]);
            $element = $generator();
            $countOf[$element] += 1;
        }
        $this->assertTrue($countOf[42] > $countOf[21]);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testFrequenciesMustBeNaturals()
    {
        frequency([
            [10, 42],
            [false, 21],
        ]);
    }
}
