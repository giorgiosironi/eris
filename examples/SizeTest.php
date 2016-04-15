<?php
use Eris\Generator;
use Eris\TestTrait;

class SizeTest extends PHPUnit_Framework_TestCase
{
    use TestTrait;

    /**
     * With the default sizes this test would pass,
     * as numbers greater or equal than 100,000 would never be reached.
     */
    public function testMaxSizeCanBeIncreased()
    {
        $this
            ->forAll(
                Generator\int()
            )
            ->withMaxSize(1000 * 1000)
            ->then(function ($number) {
                $this->assertLessThan(100 * 1000, $number);
            });
    }
}
