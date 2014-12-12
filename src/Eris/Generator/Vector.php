<?php
namespace Eris\Generator;
use Eris\Generator;

function vector($size, Generator $elementsGenerator)
{
    return new Vector($size, $elementsGenerator);
}

class Vector implements Generator
{
    private $generator;

    public function __construct($size, Generator $generator)
    {
        $this->generator = new Tuple(
            array_fill(0, $size, $generator)
        );
    }

    public function __invoke()
    {
        return $this->generator->__invoke();
    }

    public function shrink($vector)
    {
        return $this->generator->shrink($vector);
    }

    public function contains($vector)
    {
        return $this->generator->contains($vector);
    }
}
