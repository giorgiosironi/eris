<?php
namespace Eris\Generator;

use Eris\Generator;
use Eris\Generators;
use Eris\Random\RandomRange;

function bool()
{
    return Generators::bool();
}

class BooleanGenerator implements Generator
{
    public function __invoke($_size, RandomRange $rand)
    {
        $booleanValues = [true, false];
        $randomIndex = $rand->rand(0, count($booleanValues) - 1);

        return GeneratedValueSingle::fromJustValue($booleanValues[$randomIndex], 'boolean');
    }

    public function shrink(GeneratedValue $element)
    {
        return GeneratedValueSingle::fromJustValue(false);
    }
}
