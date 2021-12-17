<?php

use Eris\Generators;

class BindTest extends \PHPUnit\Framework\TestCase
{
    use Eris\TestTrait;

    public function testCreatingABrandNewGeneratorFromAGeneratedValueSingle()
    {
        $this->forAll(
            Generators::bind(
                Generators::vector(4, Generators::nat()),
                function ($vector) {
                    return Generators::tuple(
                        Generators::elements($vector),
                        Generators::constant($vector)
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
