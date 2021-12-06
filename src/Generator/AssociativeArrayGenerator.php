<?php
namespace Eris\Generator;

use Eris\Generator;
use Eris\Generators;
use Eris\Random\RandomRange;

/**
 * @return AssociativeArrayGenerator
 */
function associative(array $generators)
{
    return Generators::associative($generators);
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

    public function __invoke($size, RandomRange $rand)
    {
        $tuple = $this->tupleGenerator->__invoke($size, $rand);
        return $this->mapToAssociativeArray($tuple);
    }

    public function shrink(GeneratedValue $element)
    {
        $input = $element->input();
        $shrunkInput = $this->tupleGenerator->shrink($input);
        return $this->mapToAssociativeArray($shrunkInput);
    }

    private function mapToAssociativeArray(GeneratedValue $tuple)
    {
        return $tuple->map(
            function ($value) {
                $associativeArray = [];
                $keys = array_keys($this->generators);
                for ($i = 0, $iMax = count($value); $i < $iMax; $i++) {
                    $key = $keys[$i];
                    $associativeArray[$key] = $value[$i];
                }
                return $associativeArray;
            },
            'associative'
        );
    }
}
