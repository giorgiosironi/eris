<?php
namespace Eris\Generator;

use Eris\Generator;
use DomainException;

function vector($size, Generator $elementsGenerator)
{
    return new VectorGenerator($size, $elementsGenerator);
}

class VectorGenerator implements Generator
{
    private $generator;
    private $elementsGeneratorClass;

    public function __construct($size, Generator $generator)
    {
        $this->generator = new TupleGenerator(
            ($size > 0) ?
                array_fill(0, $size, $generator) :
                []
        );
        $this->elementsGeneratorClass = get_class($generator);
    }

    public function __invoke($size, $rand)
    {
        return $this->generator->__invoke($size, $rand);
    }

    public function shrink(GeneratedValueSingle $vector)
    {
        return $this->generator->shrink($vector);
    }
}
