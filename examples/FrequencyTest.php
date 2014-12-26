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
                $this->assertFalse(!!$falsyValue);
            });

    }
}
