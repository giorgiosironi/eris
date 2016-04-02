<?php
namespace Eris\Generator;

use Eris\Generator;

/**
 * @return AssociativeArrayGenerator
 */
function associative(array $generators)
{
    return new AssociativeArrayGenerator($generators);
}

class AssociativeArrayGenerator implements Generator
{
    private $generators;
    private $tupleGenerator;

    public function __construct(array $generators)
    {
        $this->generators = $generators;
        $this->tupleGenerator = new TupleGenerator(array_values($generators));
    }

    public function __invoke($size)
    {
        $tuple = $this->tupleGenerator->__invoke($size);
        return $this->mapToAssociativeArray($tuple);
    }

    public function shrink(GeneratedValue $element)
    {
        $input = $element->input();
        $shrunkInput = $this->tupleGenerator->shrink($input);
        return $this->mapToAssociativeArray($shrunkInput);
    }

    public function contains(GeneratedValue $element)
    {
        return $this->tupleGenerator->contains($element->input());
    }

    private function mapToAssociativeArray(GeneratedValue $tuple)
    {
        return $tuple->map(
            function($value) {
                $associativeArray = [];
                $keys = array_keys($this->generators);
                for ($i = 0; $i < count($value); $i++) {
                    $key = $keys[$i];
                    $associativeArray[$key] = $value[$i];
                }
                return $associativeArray;
            },
            'associative'
        );
    }
}
