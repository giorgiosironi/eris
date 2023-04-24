<?php

use Eris\Generators;

class VectorTest extends \PHPUnit\Framework\TestCase
{
    use Eris\TestTrait;

    public function testConcatenationMaintainsLength()
    {
        $this->forAll(
            Generators::vector(10, Generators::nat()),
            Generators::vector(10, Generators::nat())
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
