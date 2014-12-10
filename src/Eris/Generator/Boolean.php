<?php
namespace Eris\Generator;
use Eris\Generator;

function bool()
{
    return new Boolean();
}

class Boolean implements Generator
{
    public function __invoke()
    {
        $booleanValues = [true, false];
        $randomIndex = rand(0, count($booleanValues) - 1);

        return $booleanValues[$randomIndex];
    }

    public function shrink($element)
    {
        return false;
    }

    public function contains($element)
    {
        return is_bool($element);
    }
}
