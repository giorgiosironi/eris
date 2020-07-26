<?php
namespace Eris\Generator;

use Eris\Generator;
use Eris\Random\RandomRange;

function vector($size, Generator $elementsGenerator)
{
    return VectorGenerator::vector($size, $elementsGenerator);
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

    public function __invoke($size, RandomRange $rand)
    {
        return $this->generator->__invoke($size, $rand);
    }

    public function shrink(GeneratedValue $vector)
    {
        return $this->generator->shrink($vector);
    }

    public static function vector($size, Generator $elementsGenerator)
    {
        return new self($size, $elementsGenerator);
    }
}
