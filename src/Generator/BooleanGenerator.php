<?php
namespace Eris\Generator;

use Eris\Generator;
use DomainException;

function bool()
{
    return new BooleanGenerator();
}

class BooleanGenerator implements Generator
{
    public function __invoke($_size, $rand)
    {
        $booleanValues = [true, false];
        $randomIndex = $rand(0, count($booleanValues) - 1);

        return GeneratedValueSingle::fromJustValue($booleanValues[$randomIndex], 'boolean');
    }

    public function shrink(GeneratedValueSingle $element)
    {
        if (!$this->contains($element)) {
            throw new DomainException(
                $element . ' does not belong to the domain of the Booleans'
            );
        }

        return false;
    }

    public function contains(GeneratedValueSingle $element)
    {
        return is_bool($element->unbox());
    }
}
