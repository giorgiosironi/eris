<?php
namespace Eris\Generator;

use Eris\Generator;

function bool()
{
    return new BooleanGenerator();
}

class BooleanGenerator implements Generator
{
    public function __invoke($_size, \Eris\Random\RandomRange $rand)
    {
        $booleanValues = [true, false];
        $randomIndex = $rand->rand(0, count($booleanValues) - 1);

        return GeneratedValueSingle::fromJustValue($booleanValues[$randomIndex], 'boolean');
    }

    public function shrink(GeneratedValueSingle $element)
    {
        return false;
    }
}
