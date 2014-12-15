<?php
namespace Eris\Generator;
use Eris\Generator;
use DomainException;

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
        if (!$this->contains($element)) {
            throw new DomainException(
                $element . ' does not belong to the domain of the Booleans'
            );
        }

        return false;
    }

    public function contains($element)
    {
        return is_bool($element);
    }
}
