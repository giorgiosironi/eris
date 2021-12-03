<?php
namespace Eris;

use BadMethodCallException;
use DateInterval;
use Eris\Listener\MinimumEvaluations;
use Eris\Quantifier\ForAll;
use Eris\Quantifier\TimeBasedTerminationCondition;
use Eris\Random\MtRandSource;
use Eris\Random\RandomRange;
use Eris\Random\RandSource;
use Eris\Shrinker\ShrinkerFactory;

trait TestTrait
{
    // TODO: make this private as much as possible
    // TODO: it's time, extract an object?
    private $quantifiers = [];
    private $iterations = 100;
    private $listeners = [];
    private $terminationConditions = [];
    /**
     * @var RandomRange
     */
    private $randRange;
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
     * @return array
     */
    public function getTestCaseAnnotations()
    {
        if (\method_exists($this, 'getAnnotations')) {
            return $this->getAnnotations();
        }
        //from TestCase of PHPunit
        return \PHPUnit\Util\Test::parseTestMethodAnnotations(
            get_class($this),
            $this->getName(false)
        );
    }

    /**
     * @before
     */
    public function erisSetup()
    {
        $this->seedingRandomNumberGeneration();
        $this->listeners = array_filter(
            $this->listeners,
            function ($listener) {
                return !($listener instanceof MinimumEvaluations);
            }
        );
        $tags = $this->getTestCaseAnnotations();
        $this->withRand($this->getAnnotationValue($tags, 'eris-method', 'rand', 'strval'));
        $this->iterations = $this->getAnnotationValue($tags, 'eris-repeat', 100, 'intval');
        $this->shrinkingTimeLimit = $this->getAnnotationValue($tags, 'eris-shrink', null, 'intval');
        $this->listeners[] = MinimumEvaluations::ratio($this->getAnnotationValue($tags, 'eris-ratio', 50, 'floatval')/100);
        $duration = $this->getAnnotationValue($tags, 'eris-duration', false, 'strval');
        if ($duration) {
            $this->limitTo(new DateInterval($duration));
        }
    }

    /**
     * @internal
     * @return void
     */
    private function seedingRandomNumberGeneration()
    {
        $seed = intval(getenv('ERIS_SEED') ?: (microtime(true)*1000000));
        if ($seed < 0) {
            $seed *= -1;
        }
        $this->seed = $seed;
    }

    /**
     * @param array $annotations
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    private function getAnnotationValue(array $annotations, $key, $default, $cast)
    {
        $annotation = $this->getAnnotation($annotations, $key);
        return isset($annotation[0])?$cast($annotation[0]):$default;
    }

    /**
     * @param array $annotations
     * @param string $key
     * @return array
     */
    private function getAnnotation(array $annotations, $key)
    {
        if (isset($annotations['method'][$key])) {
            return $annotations['method'][$key];
        }
        return isset($annotations['class'][$key])?$annotations['class'][$key]:[];
    }

    /**
     * @after
     */
    public function erisTeardown()
    {
        $this->dumpSeedForReproducing();
    }

    /**
     * Maybe: we could add --filter options to the command here,
     * since now the original command is printed.
     */
    private function dumpSeedForReproducing()
    {
        if (! method_exists($this, 'hasFailed') || !method_exists($this, 'toString')) {
            return;
        }

        if (!$this->hasFailed()) {
            return;
        }
        $command = PHPUnitCommand::fromSeedAndName($this->seed, $this->toString());
        echo PHP_EOL."Reproduce with:".PHP_EOL.$command.PHP_EOL;
    }

    /**
     * @param float from 0.0 to 1.0
     * @return self
     */
    protected function minimumEvaluationRatio($ratio)
    {
        $this->filterOutListenersOfClass('Eris\\Listener\\MinimumEvaluations');
        $this->listeners[] = MinimumEvaluations::ratio($ratio);
        return $this;
    }

    /**
     * @param string $className
     * @return void
     */
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
     * @param integer|DateInterval $limit
     * @return self
     */
    protected function limitTo($limit)
    {
        if ($limit instanceof DateInterval) {
            $interval = $limit;
            $terminationCondition = new TimeBasedTerminationCondition('time', $interval);
            $this->listeners[] = $terminationCondition;
            $this->terminationConditions[] = $terminationCondition;
        } elseif (is_integer($limit)) {
            $this->iterations = $limit;
        } else {
            throw new \InvalidArgumentException("The limit " . var_export($limit, true) . " is not valid. Please pass an integer or DateInterval.");
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
     * @param string|RandomRange $randFunction mt_rand, rand or a RandomRange
     * @return self
     */
    protected function withRand($randFunction)
    {
        if ($randFunction === 'mt_rand') {
            $this->randRange = new RandomRange(new MtRandSource());
            return $this;
        }
        if ($randFunction === 'rand') {
            $this->randRange = new RandomRange(new RandSource());
            return $this;
        }
        if ($randFunction instanceof RandomRange) {
            $this->randRange = $randFunction;
            return $this;
        }
        throw new BadMethodCallException("When specifying random generators different from the standard ones, you must pass an instance of Eris\\Random\\RandomRange.");
    }

    /**
     * forAll($generator1, $generator2, ...)
     * @return ForAll
     */
    public function forAll()
    {
        $this->randRange->seed($this->seed);
        $generators = func_get_args();
        $quantifier = new ForAll(
            $generators,
            $this->iterations,
            new ShrinkerFactory([
                'timeLimit' => $this->shrinkingTimeLimit,
            ]),
            $this->shrinkerFactoryMethod,
            $this->randRange
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
    public function sample(Generator $generator, $times = 10, $size = null)
    {
        return Sample::of($generator, $this->randRange, $size)->repeat($times);
    }

    /**
     * @return Sample
     */
    public function sampleShrink(Generator $generator, $fromValue = null, $size = null)
    {
        return Sample::of($generator, $this->randRange, $size)->shrink($fromValue);
    }
}
