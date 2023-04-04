<?php
namespace Eris;

use BadMethodCallException;
use DateInterval;
use Doctrine\Common\Annotations\AnnotationReader;
use Eris\Attributes\ErisDuration;
use Eris\Attributes\ErisMethod;
use Eris\Attributes\ErisRatio;
use Eris\Attributes\ErisRepeat;
use Eris\Attributes\ErisShrink;
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
    public function getTestCaseAttributes()
    {
        $reflectionClass = new \ReflectionClass($this);
        $classAttributes = [];
        foreach ($reflectionClass->getAttributes() as $attribute) {
            $classAttributes[$attribute->getName()] = ($attribute)->newInstance();
        }

        $methodAttributes = [];
        foreach($reflectionClass->getMethods() as $method) {
            if($method->getName() !== $this->name()) {
                continue;
            }
            $attributes = $method->getAttributes();

            if(!empty($attributes)) {
                foreach ($attributes as $attribute) {
                    $methodAttributes[$attribute->getName()] = ($attribute)->newInstance();
                }
            }
        }

        return [
            'method' => $methodAttributes,
            'class' => $classAttributes
        ];
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
        $tags = $this->getTestCaseAttributes();
        $this->withRand($this->getAttributeValue($tags, ErisMethod::class, 'rand', 'strval'));
        $this->iterations = $this->getAttributeValue($tags, ErisRepeat::class, 100, 'intval');
        $this->shrinkingTimeLimit = $this->getAttributeValue($tags, ErisShrink::class, null, 'intval');
        $this->listeners[] = MinimumEvaluations::ratio($this->getAttributeValue($tags, ErisRatio::class, 50, 'floatval')/100);
        $duration = $this->getAttributeValue($tags, ErisDuration::class, false, 'strval');
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
     * @param array $attributes
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    private function getAttributeValue(array $attributes, $key, $default, $cast)
    {
        $attribute = $this->getAttribute($attributes, $key);
        return $attribute !== null ? $cast($attribute) : $default;
    }

    /**
     * @param array $attributes
     * @param string $key
     * @return int|string|null
     */
    private function getAttribute(array $attributes, $key)
    {
        if (isset($attributes['method'][$key])) {
            return ($attributes['method'][$key])->getValue();
        }
        return isset($attributes['class'][$key]) ? ($attributes['class'][$key])->getValue() : null;
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
