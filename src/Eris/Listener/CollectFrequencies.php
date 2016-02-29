<?php
namespace Eris\Listener;

use Eris\Generator\GeneratedValue;
use Eris\Listener;

function collectFrequencies(callable $collectFunction = null) 
{
    return new CollectFrequencies($collectFunction);
}

class CollectFrequencies
    extends EmptyListener
    implements Listener
{
    private $collectFunction;
    private $collectedValues = [];
    
    public function __construct($collectFunction = null)
    {
        if ($collectFunction === null) {
            $collectFunction = function($value) { return $value; };
        }
        $this->collectFunction = $collectFunction;
    }

    public function endPropertyVerification($evaluations)
    {
        arsort($this->collectedValues, SORT_NUMERIC);
        echo PHP_EOL;
        foreach ($this->collectedValues as $key => $value) {
            $frequency = round(($value / $evaluations) * 100, 2);
            echo "{$frequency}%  $key" . PHP_EOL;
        }
    }

    public function newGeneration(array $generatedValues)
    {
        $values = array_map(
            function($generatedValue) {
                return $generatedValue->unbox();
            },
            $generatedValues
        );
        $key = call_user_func_array($this->collectFunction, $values);
        if (array_key_exists($key, $this->collectedValues)) {
            $this->collectedValues[$key]++;
        } else {
            $this->collectedValues[$key] = 1;
        }
    }
}
