<?php
namespace Eris\Generator;

use Eris\Generator;

function oneOf(array $generators)
{
    return new OneOf($generators);
}

class OneOf implements Generator
{
    private $generator;

    public function __construct($generators) {
        $this->generator = new Frequency($this->allWithSameFrequency($generators));
    }

    public function __invoke($size)
    {
        return $this->generator->__invoke($size);
    }

    public function shrink($element)
    {
        return $this->generator->shrink($element);
    }

    public function contains($element)
    {
        return $this->generator->contains($element);
    }

    private function allWithSameFrequency($generators)
    {
        return array_map(
            function($generator) {
                return [1, $generator];
            },
            $generators
        );
    }
}
