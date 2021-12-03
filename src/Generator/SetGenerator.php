<?php
namespace Eris\Generator;

use Eris\Generator;
use Eris\Generators;
use Eris\Random\RandomRange;

/**
 * @param Generator $singleElementGenerator
 * @return SetGenerator
 */
function set($singleElementGenerator)
{
    return Generators::set($singleElementGenerator);
}

class SetGenerator implements Generator
{
    private $singleElementGenerator;

    public function __construct(Generator $singleElementGenerator)
    {
        $this->singleElementGenerator = $singleElementGenerator;
    }

    public function __invoke($size, RandomRange $rand)
    {
        $setSize = rand(0, $size);
        $set = [];
        $input = [];
        $trials = 0;
        while (count($set) < $setSize && $trials < 2 * $setSize) {
            $trials++;
            $candidateNewElement = $this->singleElementGenerator->__invoke($size, $rand);
            if (in_array($candidateNewElement->unbox(), $set, $strict = true)) {
                continue;
            }
            $set[] = $candidateNewElement->unbox();
            $input[] = $candidateNewElement;
        }
        return GeneratedValueSingle::fromValueAndInput($set, $input, 'set');
    }

    public function shrink(GeneratedValue $set)
    {
        if (count($set->input()) === 0) {
            return $set;
        }

        $input = $set->input();
        // TODO: make deterministic
        // TODO: shrink also the elements, not just the size of the set
        $indexOfElementToRemove = array_rand($input);
        unset($input[$indexOfElementToRemove]);
        $input = array_values($input);
        return GeneratedValueSingle::fromValueAndInput(
            array_map(function ($element) {
                return $element->unbox();
            }, $input),
            array_values($input),
            'set'
        );
    }
}
