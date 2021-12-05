<?php

use Eris\Generators;
use Eris\Listeners;
use Eris\TestTrait;

class LogFileTest extends PHPUnit_Framework_TestCase
{
    use TestTrait;

    public function testWritingIterationsOnALogFile()
    {
        $this
            ->forAll(
                Generators::int()
            )
            ->hook(Listeners::log(sys_get_temp_dir().'/eris-log-file-test.log'))
            ->then(function ($number) {
                $this->assertInternalType('integer', $number);
            });
    }

    public function testLogOfFailuresAndShrinking()
    {
        $this
            ->forAll(
                Generators::int()
            )
            ->hook(Listeners::log(sys_get_temp_dir().'/eris-log-file-shrinking.log'))
            ->then(function ($number) {
                $this->assertLessThanOrEqual(42, $number);
            });
    }
}
