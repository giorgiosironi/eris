<?php
namespace Eris\Listener;

use Eris\Listener;
use Eris\Listeners;
use Exception;
use InvalidArgumentException;

/**
 * @see Listeners::collectFrequencies()
 */
function collectFrequencies(?callable $collectFunction = null)
{
    return Listeners::collectFrequencies($collectFunction);
}

class CollectFrequencies extends EmptyListener implements Listener
{
    private $collectFunction;
    private array $collectedValues = [];
    
    public function __construct($collectFunction = null)
    {
        if ($collectFunction === null) {
            $collectFunction = function (/*...*/) {
                $generatedValues = func_get_args();
                $value = count($generatedValues) === 1 ? $generatedValues[0] : $generatedValues;

                if (is_string($value) || is_int($value)) {
                    return $value;
                }
                return json_encode($value);
            };
        }
        $this->collectFunction = $collectFunction;
    }

    public function endPropertyVerification($ordinaryEvaluations, $iterations, ?Exception $exception = null): void
    {
        arsort($this->collectedValues, SORT_NUMERIC);
        echo PHP_EOL;
        foreach ($this->collectedValues as $key => $value) {
            $frequency = round(($value / $ordinaryEvaluations) * 100, 2);
            echo "{$frequency}%  $key" . PHP_EOL;
        }
    }

    public function newGeneration(array $generation, $iteration): void
    {
        $key = call_user_func_array($this->collectFunction, $generation);
        // TODO: check key is a correct key, identity may lead this to be a non-string and non-integer value
        // have a default for arrays and other scalars
        if (!is_string($key) && !is_int($key)) {
            throw new InvalidArgumentException("The key " . var_export($key, true) . " cannot be used for collection, please specify a custom mapping function to collectFrequencies()");
        }
        if (array_key_exists($key, $this->collectedValues)) {
            $this->collectedValues[$key]++;
        } else {
            $this->collectedValues[$key] = 1;
        }
    }
}
