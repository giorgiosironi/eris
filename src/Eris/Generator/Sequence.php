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
    private $shrinkSize;

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
        $this->shrinkSize = new Boolean();
    }

    public function __invoke()
    {
        return $this->vector($this->sizeGenerator->__invoke())->__invoke();
    }

    public function shrink($sequence)
    {
        $willShrinkSize = $this->shrinkSize->__invoke();
        if ($willShrinkSize) {
            $indexOfElementToRemove = array_rand($sequence);
            unset($sequence[$indexOfElementToRemove]);
            $sequence = array_values($sequence);
        }
        if (!$willShrinkSize) {
            $sequence = $this->vector(count($sequence))->shrink($sequence);
        }
        return $sequence;
    }

    public function contains($sequence)
    {
        return $this->sizeGenerator->contains(count($sequence)) &&
            $this->vector(count($sequence))->contains($sequence);
    }

    private function vector($size)
    {
        return new Vector($size, $this->singleElementGenerator);
    }
}
