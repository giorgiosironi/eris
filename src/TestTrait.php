<?php
namespace Eris;

use OutOfBoundsException;
use DateInterval;
use InvalidArgumentException;

trait TestTrait
{
    // TODO: make this private as much as possible
    private $quantifiers = [];
    private $iterations = 100;
    private $listeners = [];
    private $terminationConditions = [];
    // TODO: what is the correct name for this concept?
    protected $minimumEvaluationRatio = 0.5;
    protected $seed;
    protected $shrinkingTimeLimit;

    /**
     * @before
     */
    public function seedingRandomNumberGeneration()
    {
        if ($seed = getenv('ERIS_SEED')) {
            $this->seed = $seed;
        } else {
            $this->seed = (int) (microtime(true)*1000000);
        }
        srand($this->seed);
    }

    /**
     * Maybe: we could add --filter options to the command here,
     * since now the original command is printed.
     * @after
     */
    public function dumpSeedForReproducing()
    {
        if ($this->hasFailed()) {
            global $argv;
            $command = "ERIS_SEED={$this->seed} " . implode(" ", $argv);
            echo PHP_EOL;
            echo "Reproduce with:", PHP_EOL;
            echo $command, PHP_EOL;
        }
    }

    /**
     * @beforeClass
     */
    public static function loadAllErisGenerators()
    {
        foreach(glob(__DIR__ . '/Generator/*.php') as $filename) {
            require_once($filename);
        }
    }

    /**
     * @beforeClass
     */
    public static function loadAllErisAntecedents()
    {
        foreach(glob(__DIR__ . '/Antecedent/*.php') as $filename) {
            require_once($filename);
        }
    }

    /**
     * @beforeClass
     */
    public static function loadAllErisListeners()
    {
        foreach(glob(__DIR__ . '/Listener/*.php') as $filename) {
            require_once($filename);
        }
    }

    /**
     * @after
     */
    public function checkConstraintsHaveNotSkippedTooManyIterations()
    {
        foreach ($this->quantifiers as $quantifier) {
            $evaluationRatio = $quantifier->evaluationRatio();
            if ($evaluationRatio < $this->minimumEvaluationRatio) {
                throw new OutOfBoundsException("Evaluation ratio {$evaluationRatio} is under the threshold {$this->minimumEvaluationRatio}");
            }
        }
    }

    /**
     * @param float  from 0.0 to 1.0
     * @return self
     */
    protected function minimumEvaluationRatio($ratio)
    {
        $this->minimumEvaluationRatio = $ratio;
        return $this;
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
        } else if (is_integer($limit)) {
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
     * forAll($generator1, $generator2, ...)
     */
    protected function forAll()
    {
        $generators = func_get_args();
        $quantifier = new Quantifier\ForAll(
            $generators,
            $this->iterations,
            new Shrinker\ShrinkerFactory([
                'timeLimit' => $this->shrinkingTimeLimit,
            ])
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

    protected function sample(Generator $generator, $times = 10)
    {
        return Sample::of($generator)->repeat($times);
    }

    protected function sampleShrink(Generator $generator, $fromValue = null)
    {
        return Sample::of($generator)->shrink($fromValue);
    }
}
