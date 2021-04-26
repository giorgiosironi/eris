<?php
use Eris\Generator\AssociativeArrayGenerator;
use Eris\Generator\ChooseGenerator;
use Eris\Generator\ElementsGenerator;

class AssociativeArrayTest extends PHPUnit_Framework_TestCase
{
    use Eris\TestTrait;

    public function testAssociativeArraysGeneratedOnStandardKeys()
    {
        $this->forAll(
            AssociativeArrayGenerator::associative([
                'letter' => ElementsGenerator::elements("A", "B", "C"),
                'cipher' => ChooseGenerator::choose(0, 9),
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
