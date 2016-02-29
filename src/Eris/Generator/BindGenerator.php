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
        $originalGeneratorValue = $this->originalGenerator->__invoke($size);
        $newGenerator = call_user_func($this->generatorFactory, $originalGeneratorValue->unbox());
        $newGeneratorValue = $newGenerator->__invoke($size);
        return GeneratedValue::fromValueAndInput(
            $newGeneratorValue->unbox(),
            [
                $newGeneratorValue,
                $originalGeneratorValue,
            ],
            'bind'
        );
    }

    public function shrink(GeneratedValue $element)
    {
        list ($newGeneratorValue, $originalGeneratorValue) = $element->input();
        // TODO: shrink also the second generator
        $newGenerator = call_user_func($this->generatorFactory, $originalGeneratorValue->unbox());
        $shrinkedNewGeneratorValue = $newGenerator->shrink($newGeneratorValue);
        return GeneratedValue::fromValueAndInput(
            $shrinkedNewGeneratorValue->unbox(),
            [
                $shrinkedNewGeneratorValue,
                $originalGeneratorValue,
            ],
            'bind'
        );
    }

    public function contains(GeneratedValue $element)
    {
        list ($newGeneratorValue, $originalGeneratorValue) = $element->input();
        $newGenerator = call_user_func($this->generatorFactory, $originalGeneratorValue->unbox());
        return $newGenerator->contains($newGeneratorValue);
    }
}
