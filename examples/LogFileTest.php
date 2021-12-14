<?php

use Eris\Generators;
use Eris\Listeners;
use Eris\TestTrait;

class LogFileTest extends \PHPUnit\Framework\TestCase
{
    use TestTrait;

    public function testWritingIterationsOnALogFile(): void
    {
        $this
            ->forAll(
                Generators::int()
            )
            ->hook(Listeners::log(sys_get_temp_dir().'/eris-log-file-test.log'))
            ->then(function ($number) {
                \Eris\PHPUnitDeprecationHelper::assertIsInt($number);
            });
    }

    public function testLogOfFailuresAndShrinking(): void
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
