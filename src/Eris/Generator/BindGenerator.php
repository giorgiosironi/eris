<?php
namespace Eris\Generator;

use Eris\Generator;

function bind(Generator $originalGenerator, callable $generatorFactory)
{
    return new BindGenerator(
        $originalGenerator,
        $generatorFactory
    );
}

class BindGenerator implements Generator
{
    private $originalGenerator;
    private $generatorFactory;
    
    public function __construct($originalGenerator, $generatorFactory)
    {
        $this->originalGenerator = $originalGenerator;
        $this->generatorFactory = $generatorFactory;
    }

    public function __invoke($size)
    {
        $value = $this->originalGenerator->__invoke($size);
        $newGenerator = call_user_func($this->generatorFactory, $value->unbox());
        return $newGenerator->__invoke($size);
    }

    public function shrink(GeneratedValue $element){}

    public function contains(GeneratedValue $element){}
}
