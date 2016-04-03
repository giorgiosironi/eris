<?php
use Eris\Generator;

class TupleTest extends PHPUnit_Framework_TestCase
{
    use Eris\TestTrait;

    public function testConcatenationMaintainsLength()
    {
        $this->forAll(
            Generator\tuple(
                Generator\elements("A", "B", "C"),
                Generator\choose(0, 9)
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
