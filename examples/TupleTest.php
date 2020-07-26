<?php
use Eris\Generator\ChooseGenerator;
use Eris\Generator\ElementsGenerator;
use Eris\Generator\TupleGenerator;

class TupleTest extends PHPUnit_Framework_TestCase
{
    use Eris\TestTrait;

    public function testConcatenationMaintainsLength()
    {
        $this->forAll(
            TupleGenerator::tuple(
                ElementsGenerator::elements("A", "B", "C"),
                ChooseGenerator::choose(0, 9)
            )
        )
            ->then(function ($tuple) {
                $letter = $tuple[0];
                $cipher = $tuple[1];
                $this->assertEquals(
                    2,
                    strlen($letter . $cipher),
                    "{$letter}{$cipher} is not a 2-char string"
                );
            });
    }
}
