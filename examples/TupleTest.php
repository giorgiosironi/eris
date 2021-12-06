<?php

use Eris\Generators;

class TupleTest extends \PHPUnit\Framework\TestCase
{
    use Eris\TestTrait;

    public function testConcatenationMaintainsLength()
    {
        $this->forAll(
            Generators::tuple(
                Generators::elements("A", "B", "C"),
                Generators::choose(0, 9)
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
