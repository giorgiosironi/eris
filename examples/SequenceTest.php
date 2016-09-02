<?php
use Eris\Generator;
use Eris\Listener;

class SequenceTest extends PHPUnit_Framework_TestCase
{
    use Eris\TestTrait;

    public function testArrayReversePreserveLength()
    {
        $this
            ->forAll(
                Generator\seq(Generator\nat())
            )
            ->then(function ($array) {
                $this->assertEquals(count($array), count(array_reverse($array)));
            });
    }

    public function testArrayReverse()
    {
        $this
            ->forAll(
                Generator\seq(Generator\nat())
            )
            ->then(function ($array) {
                $this->assertEquals($array, array_reverse(array_reverse($array)));
            });
    }

    public function testArraySortingIsIdempotent()
    {
        $this
            ->forAll(
                Generator\seq(Generator\nat())
            )
            ->then(function ($array) {
                sort($array);
                $expected = $array;
                sort($array);
                $this->assertEquals($expected, $array);
            });
    }
}
