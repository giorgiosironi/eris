<?php
use Eris\Generator;

class SequenceTest extends PHPUnit_Framework_TestCase
{
    use Eris\TestTrait;

    public function testArrayReversePreserveLength()
    {
        $this
            ->forAll(
                Generator\seq(Generator\nat(), 100)
            )
            ->then(function($array) {
                $this->assertEquals(count($array), count(array_reverse($array)));
            });
    }

    public function testArrayReverse()
    {
        $this
            ->forAll(
                Generator\seq(
                    Generator\nat(),
                    Generator\pos(100)
                )
            )
            ->then(function($array) {
                $this->assertEquals($array, array_reverse(array_reverse($array)));
            });
    }
}
