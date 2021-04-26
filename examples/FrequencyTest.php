<?php
use Eris\Generator\ChooseGenerator;
use Eris\Generator\FrequencyGenerator;

class FrequencyTest extends \PHPUnit_Framework_TestCase
{
    use Eris\TestTrait;

    public function testFalsyValues()
    {
        $this
            ->forAll(
                FrequencyGenerator::frequency(
                    [8, false],
                    [4, 0],
                    [4, '']
                )
            )
            ->then(function ($falsyValue) {
                $this->assertFalse((bool) $falsyValue);
            });
    }

    public function testAlwaysFails()
    {
        $this
            ->forAll(
                FrequencyGenerator::frequency(
                    [8, ChooseGenerator::choose(1, 100)],
                    [4, ChooseGenerator::choose(100, 200)],
                    [4, ChooseGenerator::choose(200, 300)]
                )
            )
            ->then(function ($element) {
                $this->assertEquals(0, $element);
            });
    }
}
