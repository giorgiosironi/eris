<?php
use Eris\Generator;

class FrequencyTest extends \PHPUnit_Framework_TestCase
{
    use Eris\TestTrait;

    public function testFalsyValues()
    {
        $this
            ->forAll([
                Generator\frequency([
                    [8, false],
                    [4, 0],
                    [4, ''],
                ])
            ])
            ->then(function($falsyValue) {
                $this->assertFalse((bool) $falsyValue);
            });
    }

    public function testAlwaysFails()
    {
        $this
            ->forAll([
                Generator\frequency([
                    [8, Generator\nat(1, 100)],
                    [4, Generator\nat(100, 200)],
                    [4, Generator\nat(200, 300)],
                ])
            ])
            ->then(function($element) {
                // Expected to shrunk always to 1
                $this->assertEquals(0, $element);
            });
    }
}
