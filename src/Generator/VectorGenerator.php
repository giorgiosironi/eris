<?php
namespace Eris\Generator;

use Eris\Generator;
use Eris\Generators;
use Eris\Random\RandomRange;

/**
 * @template T
 * @param int $size
 * @param Generator<T> $elementsGenerator
 * @return VectorGenerator<T>
 */
function vector($size, Generator $elementsGenerator)
{
    return Generators::vector($size, $elementsGenerator);
}

/**
 * @template-covariant T
 * @template-implements Generator<list<T>>
 */
class VectorGenerator implements Generator
{
    private $generator;
    private $elementsGeneratorClass;

    /**
     * @param int $size
     * @param Generator<T> $generator
     */
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
}
