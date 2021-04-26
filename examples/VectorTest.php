<?php
use Eris\Generator\IntegerGenerator;
use Eris\Generator\VectorGenerator;

class VectorTest extends PHPUnit_Framework_TestCase
{
    use Eris\TestTrait;

    public function testConcatenationMaintainsLength()
    {
        $this->forAll(
            VectorGenerator::vector(10, IntegerGenerator::nat(1000)),
            VectorGenerator::vector(10, IntegerGenerator::nat(1000))
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
