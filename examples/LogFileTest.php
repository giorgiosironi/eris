<?php
use Eris\Generator;
use Eris\TestTrait;
use Eris\Listener;

class LogFileTest extends PHPUnit_Framework_TestCase
{
    use TestTrait;

    public function testSumIsCommutative()
    {
        $this->forAll(
            Generator\int()
        )
            ->hook(Listener\log('/tmp/eris-log-file-test.log'))
            ->then(function($number) {
                $this->assertInternalType('integer', $number);
            });
    }
}

