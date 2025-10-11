<?php

use Eris\Generators;

class BindTest extends \PHPUnit\Framework\TestCase
{
    use Eris\TestTrait;

    public function testCreatingABrandNewGeneratorFromAGeneratedValueSingle(): void
    {
        $this->forAll(
            Generators::bind(
                Generators::vector(4, Generators::nat()),
                fn ($vector): \Eris\Generator\TupleGenerator => Generators::tuple(
                    Generators::elements($vector),
                    Generators::constant($vector)
                )
            )
        )
            ->then(function ($tuple): void {
                [$element, $vector] = $tuple;
                $this->assertContains($element, $vector);
            });
    }

    // TODO: multiple generators means multiple values passed to the
    // outer Generator factory
}
