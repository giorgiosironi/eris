<?php
use Eris\Generator;

class AssociativeArrayTest extends PHPUnit_Framework_TestCase
{
    use Eris\TestTrait;

    public function testAssociativeArraysGeneratedOnStandardKeys()
    {
        $this->forAll(
            Generator\associative([
                'letter' => Generator\elements("A", "B", "C"),
                'cipher' => Generator\choose(0, 9),
            ])
        )
            ->then(function ($array) {
                $this->assertEquals(2, count($array));
                $letter = $array['letter'];
                $this->assertInternalType('string', $letter);
                $cipher = $array['cipher'];
                $this->assertInternalType('integer', $cipher);
            });
    }
}
