<?php
use Eris\Generator;
use Eris\TestTrait;
use Eris\Listener;

class ShrinkingMultipleTest extends \PHPUnit_Framework_TestCase
{
    use TestTrait;

    public function testShrinkingThroughMultipleOptions()
    {
        $this->forAll(
                Generator\int()
            )
            ->hook(Listener\log('/tmp/eris-multiple-shrinking.log'))
            ->withMaxSize(1000 * 1000)
            ->then(function ($number) {
                $this->assertLessThanOrEqual(5000, $number);
            });
    }
}
