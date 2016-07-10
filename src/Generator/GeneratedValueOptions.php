<?php
namespace Eris\Generator;

use IteratorAggregate;
use ArrayIterator;
use Countable;

class GeneratedValueOptions
    extends GeneratedValue 
    implements IteratorAggregate, Countable
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

    public function count()
    {
        return count($this->generatedValues);
    }

    public function cartesianProduct($generatedValueOptions, callable $merge) 
    {
        $options = [];
        foreach ($this as $firstPart) {
            foreach ($generatedValueOptions as $secondPart) {
                $options[] = $firstPart->merge($secondPart, $merge);
            }
        }
        return new self($options);
    }
}
