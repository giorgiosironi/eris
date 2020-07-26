<?php

use Eris\Generator\BindGenerator;
use Eris\Generator\ConstantGenerator;
use Eris\Generator\ElementsGenerator;
use Eris\Generator\IntegerGenerator;
use Eris\Generator\TupleGenerator;
use Eris\Generator\VectorGenerator;

class BindTest extends PHPUnit_Framework_TestCase
{
    use Eris\TestTrait;

    public function testCreatingABrandNewGeneratorFromAGeneratedValueSingle()
    {
        $this->forAll(
            BindGenerator::bind(
                VectorGenerator::vector(4, IntegerGenerator::nat()),
                function ($vector) {
                    return TupleGenerator::tuple(
                        ElementsGenerator::elements($vector),
                        ConstantGenerator::constant($vector)
                    );
                }
            )
        )
            ->then(function ($tuple) {
                list($element, $vector) = $tuple;
                $this->assertContains($element, $vector);
            });
    }

    // TODO: multiple generators means multiple values passed to the
    // outer Generator factory
}
