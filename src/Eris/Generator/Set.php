<?php
namespace Eris\Generator;

use Eris\Generator;
use DomainException;

/**
 * @param Generator|array $singleElementGenerator
 * @return Set
 */
function set($input)
{
    if (is_array($input)) {
        $singleElementGenerator = new Generator\OneOf(array_map(
            'Eris\Generator\Constant::box',
            $input
        ));
    } else {
        $singleElementGenerator = $input;
    }
    return new Set($singleElementGenerator);
}

class Set implements Generator
{
    private $singleElementGenerator;

    public function __construct(Generator $singleElementGenerator)
    {
        $this->singleElementGenerator = $singleElementGenerator;
    }

    public function __invoke($size)
    {
        $setSize = rand(0, $size);
        $set = [];
        $trials = 0;
        while (count($set) < $setSize && $trials < 1000) {
            $trials++;
            $candidateNewElement = $this->singleElementGenerator->__invoke($size);
            // TODO: avoid infinite loops
            if (in_array($candidateNewElement, $set, $strict = true)) {
                continue;
            }
            $set[] = $candidateNewElement;
        }
        return $set;
    }

    public function shrink($set)
    {
        // TODO: extract duplication with Generator\Sequence
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

        if (count($set) === 0) {
            return $set;
        }

        $indexOfElementToRemove = array_rand($set);
        unset($set[$indexOfElementToRemove]);
        return array_values($set);
    }

    public function contains($set)
    {
        foreach ($set as $element) {
            if (!$this->singleElementGenerator->contains($element)) {
                return false;
            }
        }
        return true;
    }
}
