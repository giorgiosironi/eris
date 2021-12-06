<?php

use Eris\Generators;

class AssociativeArrayTest extends \PHPUnit\Framework\TestCase
{
    use Eris\TestTrait;

    public function testAssociativeArraysGeneratedOnStandardKeys()
    {
        $this->forAll(
            Generators::associative([
                'letter' => Generators::elements("A", "B", "C"),
                'cipher' => Generators::choose(0, 9),
            ])
        )
            ->then(function ($array) {
                $this->assertCount(2, $array);
                $letter = $array['letter'];
                $this->assertInternalType('string', $letter);
                $cipher = $array['cipher'];
                $this->assertInternalType('integer', $cipher);
            });
    }
}
