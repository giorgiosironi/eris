<?php
namespace Eris\Generator;
use Eris\Generator;
use DomainException;

function seq(Generator $singleElementGenerator, $sizeGenerator = 1000)
{
    // TODO: Generator::box($singleElementGenerator);
    if (!($singleElementGenerator instanceof Generator)) {
        $singleElementGenerator = new Constant($singleElementGenerator);
    }
    // TODO: Generator::box($currentSize);
    if (!($sizeGenerator instanceof Generator)) {
        $sizeGenerator = new Constant($sizeGenerator);
    }
    return new Sequence($singleElementGenerator, $sizeGenerator);
}

class Sequence implements Generator
{
    private $singleElementGenerator;
    private $sizeGenerator;

    public function __construct(Generator $singleElementGenerator, Generator $currentSize)
    {
        $this->singleElementGenerator = $singleElementGenerator;
        $this->sizeGenerator = $currentSize;
    }

    public function __invoke()
    {
        return $this->vector($this->sizeGenerator->__invoke())->__invoke();
    }

    public function shrink($sequence)
    {
        if (!$this->contains($sequence)) {
            throw new DomainException(
                'Cannot shrink {' . var_export($sequence, true) . '} because ' .
                'it does not belong to the domain of this sequence'
            );
        }

        $willShrinkInSize = (new Boolean())->__invoke();
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
        return new Vector($size, $this->singleElementGenerator);
    }
}
