<?php
use Eris\Generator;

class FrequencyTest extends \PHPUnit_Framework_TestCase
{
    use Eris\TestTrait;

    public function testFalsyValues()
    {
        $this
            ->forAll(
                Generator\frequency(
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
                Generator\frequency(
                    [8, Generator\choose(1, 100)],
                    [4, Generator\choose(100, 200)],
                    [4, Generator\choose(200, 300)]
                )
            )
            ->then(function ($element) {
                $this->assertEquals(0, $element);
            });
    }
}
