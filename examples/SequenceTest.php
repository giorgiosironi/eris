<?php

use Eris\Generators;

class SequenceTest extends PHPUnit_Framework_TestCase
{
    use Eris\TestTrait;

    public function testArrayReversePreserveLength()
    {
        $this
            ->forAll(
                Generators::seq(Generators::nat())
            )
            ->then(function ($array) {
                $this->assertEquals(count($array), count(array_reverse($array)));
            });
    }

    public function testArrayReverse()
    {
        $this
            ->forAll(
                Generators::seq(Generators::nat())
            )
            ->then(function ($array) {
                $this->assertEquals($array, array_reverse(array_reverse($array)));
            });
    }

    public function testArraySortingIsIdempotent()
    {
        $this
            ->forAll(
                Generators::seq(Generators::nat())
            )
            ->then(function ($array) {
                sort($array);
                $expected = $array;
                sort($array);
                $this->assertEquals($expected, $array);
            });
    }
}
