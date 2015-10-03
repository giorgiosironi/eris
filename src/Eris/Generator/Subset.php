<?php
namespace Eris\Generator;

use Eris\Generator;
use DomainException;

// TODO: accept also a list? OneOf?
function subset(Generator $singleElementGenerator)
{
    // TODO: Generator::box($singleElementGenerator);
    if (!($singleElementGenerator instanceof Generator)) {
        $singleElementGenerator = new Constant($singleElementGenerator);
    }
    return new Sequence($singleElementGenerator);
}

class Subset// implements Generator
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

}
