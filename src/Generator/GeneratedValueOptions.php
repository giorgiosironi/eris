<?php
namespace Eris\Generator;

class GeneratedValueOptions extends GeneratedValue
{
    private $generatedValues;
    
    public function __construct(array $generatedValues)
    {
        $this->generatedValues = $generatedValues;
    }

    public function first()
    {
        return $this->generatedValues[0];
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
}
