<?php
namespace Eris\Listener;

use Eris\Listener;
use InvalidArgumentException;
use Exception;

function collectFrequencies(callable $collectFunction = null)
{
    return new CollectFrequencies($collectFunction);
}

class CollectFrequencies extends EmptyListener implements Listener
{
    private $collectFunction;
    private $collectedValues = [];
    
    public function __construct($collectFunction = null)
    {
        if ($collectFunction === null) {
            $collectFunction = function (/*...*/) {
                $generatedValues = func_get_args();
                if (count($generatedValues) === 1) {
                    $value = $generatedValues[0];
                } else {
                    $value = $generatedValues;
                }

                if (is_string($value) || is_integer($value)) {
                    return $value;
                } else {
                    return json_encode($value);
                }
            };
        }
        $this->collectFunction = $collectFunction;
    }

    public function endPropertyVerification($ordinaryEvaluations, $iterations, Exception $exception = null)
    {
        arsort($this->collectedValues, SORT_NUMERIC);
        echo PHP_EOL;
        foreach ($this->collectedValues as $key => $value) {
            $frequency = round(($value / $ordinaryEvaluations) * 100, 2);
            echo "{$frequency}%  $key" . PHP_EOL;
        }
    }

    public function newGeneration(array $generation, $iteration)
    {
        $key = call_user_func_array($this->collectFunction, $generation);
        // TODO: check key is a correct key, identity may lead this to be a non-string and non-integer value
        // have a default for arrays and other scalars
        if (!is_string($key) && !is_integer($key)) {
            throw new InvalidArgumentException("The key " . var_export($key, true) . " cannot be used for collection, please specify a custom mapping function to collectFrequencies()");
        }
        if (array_key_exists($key, $this->collectedValues)) {
            $this->collectedValues[$key]++;
        } else {
            $this->collectedValues[$key] = 1;
        }
    }
}
