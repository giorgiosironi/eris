<?php
namespace Eris\Generator;

use Eris\Generator;
use DomainException;

function seq(Generator $singleElementGenerator)
{
    // TODO: Generator::box($singleElementGenerator);
    if (!($singleElementGenerator instanceof Generator)) {
        $singleElementGenerator = new Constant($singleElementGenerator);
    }
    return new SequenceGenerator($singleElementGenerator);
}

class SequenceGenerator implements Generator
{
    private $singleElementGenerator;

    public function __construct(Generator $singleElementGenerator)
    {
        $this->singleElementGenerator = $singleElementGenerator;
    }

    public function __invoke($size)
    {
        $sequenceLength = rand(0, $size);
        return $this->vector($sequenceLength)->__invoke($size);
    }

    public function shrink($sequence)
    {
        if (!$this->contains($sequence)) {
            throw new DomainException(
                'Cannot shrink {' . var_export($sequence, true) . '} because ' .
                'it does not belong to the domain of this sequence'
            );
        }

        $willShrinkInSize = (new BooleanGenerator())->__invoke(1);
        if ($willShrinkInSize) {
            $sequence = $this->shrinkInSize($sequence);
        }
        if (!$willShrinkInSize) {
            $sequence = $this->shrinkTheElements($sequence);
        }
        return $sequence;
    }

    public function contains($sequence)
    {
        return $this->vector(count($sequence))->contains($sequence);
    }

    private function shrinkInSize($sequence)
    {
        if (count($sequence) === 0) {
            return $sequence;
        }

        $indexOfElementToRemove = array_rand($sequence);
        unset($sequence[$indexOfElementToRemove]);
        return array_values($sequence);
    }

    private function shrinkTheElements($sequence)
    {
        return $this->vector(count($sequence))->shrink($sequence);
    }

    private function vector($size)
    {
        return new VectorGenerator($size, $this->singleElementGenerator);
    }
}
