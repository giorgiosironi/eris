<?php
namespace Eris;

use OutOfBoundsException;
use DateInterval;
use InvalidArgumentException;

trait TestTrait
{
    // TODO: make this private as much as possible
    // TODO: it's time, extract an object?
    private $quantifiers = [];
    private $iterations = 100;
    private $listeners = [];
    private $terminationConditions = [];
    private $randFunction = 'rand';
    private $seedFunction = 'srand';
    private $shrinkerFactoryMethod = 'multiple';
    protected $seed;
    protected $shrinkingTimeLimit;

    /**
     * @beforeClass
     */
    public static function erisSetupBeforeClass()
    {
        foreach (['Generator', 'Antecedent', 'Listener', 'Random'] as $namespace) {
            foreach (glob(__DIR__ . '/' . $namespace . '/*.php') as $filename) {
                require_once($filename);
            }
        }
    }

    /**
     * @before
     */
    public function erisSetup()
    {
        $this->seedingRandomNumberGeneration();
        $this->minimumEvaluationRatio(0.5);
    }

    /**
     * @after
     */
    public function erisTeardown()
    {
        $this->dumpSeedForReproducing();
    }

    private function seedingRandomNumberGeneration()
    {
        if ($seed = getenv('ERIS_SEED')) {
            $this->seed = $seed;
        } else {
            $this->seed = (int) (microtime(true)*1000000);
        }
    }

    /**
     * Maybe: we could add --filter options to the command here,
     * since now the original command is printed.
     */
    private function dumpSeedForReproducing()
    {
        if ($this->hasFailed()) {
            global $argv;
            $command = PHPUnitCommand::fromSeedAndName($this->seed, $this->toString());
            echo PHP_EOL;
            echo "Reproduce with:", PHP_EOL;
            echo $command, PHP_EOL;
        }
    }

    /**
     * @param float  from 0.0 to 1.0
     * @return self
     */
    protected function minimumEvaluationRatio($ratio)
    {
        $this->filterOutListenersOfClass('Eris\\Listener\\MinimumEvaluations');
        $this->listeners[] = Listener\MinimumEvaluations::ratio($ratio);
        return $this;
    }

    private function filterOutListenersOfClass($className)
    {
        $this->listeners = array_filter(
            $this->listeners,
            function ($listener) use ($className) {
                return !($listener instanceof $className);
            }
        );
    }

    /**
     * @param integer|DateInterval
     * @return self
     */
    protected function limitTo($limit)
    {
        if ($limit instanceof DateInterval) {
            $interval = $limit;
            $terminationCondition = new Quantifier\TimeBasedTerminationCondition('time', $interval);
            $this->listeners[] = $terminationCondition;
            $this->terminationConditions[] = $terminationCondition;
        } elseif (is_integer($limit)) {
            $this->iterations = $limit;
        } else {
            throw new InvalidArgumentException("The limit " . var_export($limit, true) . " is not valid. Please pass an integer or DateInterval.");
        }
        return $this;
    }

    /**
     * The maximum time to spend trying to shrink the input after a failed test.
     * The default is no limit.
     *
     * @param integer  in seconds
     * @return self
     */
    protected function shrinkingTimeLimit($shrinkingTimeLimit)
    {
        $this->shrinkingTimeLimit = $shrinkingTimeLimit;
        return $this;
    }

    /**
     * @return self
     */
    protected function withRand($randFunction)
    {
        // TODO: invert and wrap rand, srand into objects?
        if ($randFunction instanceof \Eris\Random\RandomRange) {
            $this->randFunction = function ($lower = null, $upper = null) use ($randFunction) {
                return $randFunction->rand($lower, $upper);
            };
            $this->seedFunction = function ($seed) use ($randFunction) {
                return $randFunction->seed($seed);
            };
        }
        if (is_callable($randFunction)) {
            switch ($randFunction) {
                case 'rand':
                    $seedFunction = 'srand';
                    break;
                case 'mt_rand':
                    $seedFunction = 'mt_srand';
                    break;
                default:
                    throw new BadMethodCallException("When specifying random generators different from the standard ones, you must also pass a \$seedFunction callable that will be called to seed it.");
            }
            $this->randFunction = $randFunction;
            $this->seedFunction = $seedFunction;
        }
        return $this;
    }

    /**
     * forAll($generator1, $generator2, ...)
     * @return Quantifier\ForAll
     */
    public function forAll()
    {
        call_user_func($this->seedFunction, $this->seed);
        $generators = func_get_args();
        $quantifier = new Quantifier\ForAll(
            $generators,
            $this->iterations,
            new Shrinker\ShrinkerFactory([
                'timeLimit' => $this->shrinkingTimeLimit,
            ]),
            $this->shrinkerFactoryMethod,
            $this->randFunction
        );
        foreach ($this->listeners as $listener) {
            $quantifier->hook($listener);
        }
        foreach ($this->terminationConditions as $terminationCondition) {
            $quantifier->stopOn($terminationCondition);
        }
        $this->quantifiers[] = $quantifier;
        return $quantifier;
    }

    /**
     * @return Sample
     */
    public function sample(Generator $generator, $times = 10)
    {
        return Sample::of($generator, $this->randFunction)->repeat($times);
    }

    /**
     * @return Sample
     */
    public function sampleShrink(Generator $generator, $fromValue = null)
    {
        return Sample::of($generator, $this->randFunction)->shrink($fromValue);
    }
}
