<?php
use Eris\Generator;

class FloatTest extends \PHPUnit_Framework_TestCase
{
    use Eris\TestTrait;

    public function testElementsOnlyProducesElementsFromTheGivenArguments()
    {
        $this->forAll([
            Generator\float(-100.0, 100.0),
        ])
            ->then(function($number) {
                $this->assertEquals(
                    0.0,
                    $number - $number
                );
            });
    }
}
