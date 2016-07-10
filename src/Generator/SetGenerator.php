<?php
namespace Eris\Generator;

use Eris\Generator;
use DomainException;

/**
 * @param Generator $singleElementGenerator
 * @return SetGenerator
 */
function set($singleElementGenerator)
{
    return new SetGenerator($singleElementGenerator);
}

class SetGenerator implements Generator
{
    private $singleElementGenerator;

    public function __construct(Generator $singleElementGenerator)
    {
        $this->singleElementGenerator = $singleElementGenerator;
    }

    public function __invoke($size, $rand)
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
        return GeneratedValue::fromValueAndInput($set, $input, 'set');
    }

    public function shrink(GeneratedValue $set)
    {
        // TODO: extract duplication with Generator\SequenceGenerator
        // to do so, implement __toString for every Generator (put it
        // in the interface) and then Extract Class
        // ContainmentCheck::of($this)->on($set);
        // which will use $generator->__toString() in the error message
        if (!$this->contains($set)) {
            throw new DomainException(
                'Cannot shrink {' . var_export($set, true) . '} because ' .
                'it does not belong to the domain of this set'
            );
        }

        if (count($set->input()) === 0) {
            return $set;
        }

        $input = $set->input();
        // TODO: make deterministic
        // TODO: shrink also the elements, not just the size of the set
        $indexOfElementToRemove = array_rand($input);
        unset($input[$indexOfElementToRemove]);
        $input = array_values($input);
        return GeneratedValue::fromValueAndInput(
            array_map(function ($element) {
                return $element->unbox();
            }, $input),
            array_values($input),
            'set'
        );
    }

    public function contains(GeneratedValue $set)
    {
        foreach ($set->input() as $element) {
            if (!$this->singleElementGenerator->contains($element)) {
                return false;
            }
        }
        return true;
    }
}
