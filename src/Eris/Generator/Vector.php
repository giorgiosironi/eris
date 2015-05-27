<?php
namespace Eris\Generator;

use Eris\Generator;
use DomainException;

function vector($size, Generator $elementsGenerator)
{
    return new Vector($size, $elementsGenerator);
}

class Vector implements Generator
{
    private $generator;
    private $elementsGeneratorClass;

    public function __construct($size, Generator $generator)
    {
        $this->generator = new Tuple(
            ($size > 0) ?
                array_fill(0, $size, $generator) :
                []
        );
        $this->elementsGeneratorClass = get_class($generator);
    }

    public function __invoke()
    {
        return $this->generator->__invoke();
    }

    public function shrink($vector)
    {
        if (!$this->contains($vector)) {
            throw new DomainException(
                'Cannot shrink ' . var_export($vector, true) . ' because it does not ' .
                'belong to the domain of the Vectors with '  . count($this->generator) .
                ' elements of type ' . $this->elementsGeneratorClass
            );
        }
        return $this->generator->shrink($vector);
    }

    public function contains($vector)
    {
        return $this->generator->contains($vector);
    }
}
