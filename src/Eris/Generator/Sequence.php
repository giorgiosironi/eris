<?php
namespace Eris\Generator;
use Eris\Generator;
use InvalidArgumentException;

function seq(Generator $singleElementGenerator, $currentSize = 1000)
{
    return new Sequence($singleElementGenerator, $currentSize);
}

class Sequence implements Generator
{
    private $sizeGenerator;
    private $singleElementGenerator;

    public function __construct($singleElementGenerator, $currentSize)
    {
        $this->singleElementGenerator = $singleElementGenerator;
        $this->sizeGenerator = $currentSize;
        if (!($this->singleElementGenerator instanceof Generator)) {
            $this->singleElementGenerator = new Constant($this->singleElementGenerator);
        }
        if (!($this->sizeGenerator instanceof Generator)) {
            $this->sizeGenerator = new Constant($this->sizeGenerator);
        }
    }

    public function __invoke()
    {
        return $this->vector($this->sizeGenerator->__invoke())->__invoke();
    }

    public function shrink($sequence)
    {
        if (!$this->contains($sequence)) {
            throw new InvalidArgumentException(
                'Cannot shrink {' . var_export($sequence, true) . '} because ' .
                'it does not belongs to the domain of this sequence'
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
