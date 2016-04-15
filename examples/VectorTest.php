<?php
use Eris\Generator;

class VectorTest extends PHPUnit_Framework_TestCase
{
    use Eris\TestTrait;

    public function testConcatenationMaintainsLength()
    {
        $this->forAll(
            Generator\vector(10, Generator\nat(1000)),
            Generator\vector(10, Generator\nat(1000))
        )
            ->then(function ($first, $second) {
                $concatenated = array_merge($first, $second);
                $this->assertEquals(
                    count($concatenated),
                    count($first) + count($second),
                    var_export($first, true) . " and " . var_export($second, true) . " do not maintain their length when concatenated."
                );
            });
    }
}
