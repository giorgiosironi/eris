<?php
namespace Eris\Generator;

use IteratorAggregate;
use ArrayIterator;

class GeneratedValueOptions extends GeneratedValue implements IteratorAggregate
{
    private $generatedValues;
    
    public function __construct(array $generatedValues)
    {
        $this->generatedValues = $generatedValues;
    }

    public function first()
    {
        return $this->generatedValues[count($this->generatedValues) - 1];
    }

    public function map(callable $callable, $passthru)
    {
        return new self(array_map(
            function ($value) use ($callable, $passthru) {
                return $value->map($callable, $passthru);
            },
            $this->generatedValues
        ));
    }

    public function unbox()
    {
        return $this->first()->unbox();
    }

    public function input()
    {
        return $this->first()->input();
    }

    public function getIterator()
    {
        return new ArrayIterator($this->generatedValues);
    }
}
