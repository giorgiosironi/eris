<?php
namespace Eris\Quantifier;

use Eris\Antecedent;
use Eris\Generator;
use Eris\Generator\GeneratedValue;
use Eris\Shrinker;
use BadMethodCallException;
use PHPUnit_Framework_Constraint;
use PHPUnit_Framework_AssertionFailedError;
use PHPUnit_Framework_ExpectationFailedException;
use Exception;
use RuntimeException;
use Eris\Listener;

class ForAll
{
    const DEFAULT_MAX_SIZE = 200;

    private $generators;
    private $iterations;
    private $maxSize;
    private $shrinkerFactory;
    private $antecedents = [];
    private $ordinaryEvaluations = 0;
    private $aliases = [
        'and' => 'when',
        'then' => '__invoke',
    ];
    private $terminationConditions = [];
    private $listeners = [];
    private $rand;
    private $shrinkingEnabled = true;

    public function __construct(array $generators, $iterations, $shrinkerFactory, $rand)
    {
        $this->generators = $this->generatorsFrom($generators);
        $this->iterations = $iterations;
        $this->shrinkerFactory = $shrinkerFactory;
        $this->rand = $rand;
        $this->maxSize = self::DEFAULT_MAX_SIZE;
    }

    /**
     * @param integer $maxSize
     * @return self
     */
    public function withMaxSize($maxSize)
    {
        $this->maxSize = $maxSize;
        return $this;
    }

    /**
     * @return self
     */
    public function hook(Listener $listener)
    {
        $this->listeners[] = $listener;
        return $this;
    }

    /**
     * @return self
     */
    public function stopOn(TerminationCondition $terminationCondition)
    {
        $this->terminationConditions[] = $terminationCondition;
        return $this;
    }

    /**
     * @return self
     */
    public function disableShrinking()
    {
        $this->shrinkingEnabled = false;
        return $this;
    }

    /**
     * Examples of calls:
     * when($constraint1, $constraint2, ..., $constraintN)
     * when(callable $takesNArguments)
     * when(Antecedent $antecedent)
     * @return self
     */
    public function when(/* see docblock */)
    {
        $arguments = func_get_args();
        if ($arguments[0] instanceof Antecedent) {
            $antecedent = $arguments[0];
        } else if ($arguments[0] instanceof PHPUnit_Framework_Constraint) {
            $antecedent = Antecedent\IndependentConstraintsAntecedent::fromAll($arguments);
        } else if ($arguments && count($arguments) == 1) {
            $antecedent = Antecedent\SingleCallbackAntecedent::from($arguments[0]);
        } else {
            throw new \InvalidArgumentException("Invalid call to when(): " . var_export($arguments, true));
        }
        $this->antecedents[] = $antecedent;
        return $this;
    }

    public function __invoke(callable $assertion)
    {
        $sizes = Size::withTriangleGrowth($this->maxSize)
            ->limit($this->iterations);
        try {
            $this->notifyListeners('startPropertyVerification');
            for (
                $iteration = 0;
                $iteration < $this->iterations
                && !$this->terminationConditionsAreSatisfied();
                $iteration++
            ) {
                $generatedValues = [];
                $values = [];
                foreach ($this->generators as $name => $generator) {
                    $value = $generator($sizes->at($iteration), $this->rand);
                    if (!($value instanceof GeneratedValue)) {
                        throw new RuntimeException("The value returned by a generator should be an instance of GeneratedValue, but it is " . var_export($value, true));
                    }
                    $generatedValues[] = $value;
                    $values[] = $value->unbox();
                }
                $this->notifyListeners('newGeneration', $generatedValues, $iteration);

                if (!$this->antecedentsAreSatisfied($values)) {
                    continue;
                }

                $this->ordinaryEvaluations++;
                Evaluation::of($assertion)
                    // TODO: coupling between here and the TupleGenerator used inside?
                    ->with(GeneratedValue::fromValueAndInput(
                        $values,
                        $generatedValues, 
                        'tuple'
                    ))
                    ->onFailure(function($generatedValues, $exception) use ($assertion) {
                        if (!$this->shrinkingEnabled) {
                            throw $exception;
                        }
                        $shrinking = $this->shrinkerFactory->random($this->generators, $assertion);
                        // MAYBE: put into ShrinkerFactory?
                        $shrinking->addGoodShrinkCondition(function(GeneratedValue $generatedValues) {
                            return $this->antecedentsAreSatisfied($generatedValues->unbox());
                        });
                        $shrinking->from($generatedValues, $exception);
                    })
                    ->execute();
            }
        } catch (Exception $e) {
            $wrap = (bool) getenv('ERIS_ORIGINAL_INPUT');
            if ($wrap) {
                $message = "Original input: " . var_export($values, true) . PHP_EOL
                    . "Possibly shrinked input follows." . PHP_EOL;
                throw new RuntimeException($message, -1, $e);
            } else {
                throw $e;
            }
        } finally {
            $this->notifyListeners('endPropertyVerification', $this->ordinaryEvaluations);
        }
    }

    /**
     * @see $this->aliases
     * @method then($assertion)
     * @method implies($assertion)
     * @method imply($assertion)
     */
    public function __call($method, $arguments)
    {
        if (isset($this->aliases[$method])) {
            return call_user_func_array(
                [$this, $this->aliases[$method]],
                $arguments
            );
        }
        throw new BadMethodCallException("Method " . __CLASS__ . "::{$method} does not exist");
    }

    public function evaluationRatio()
    {
        return $this->ordinaryEvaluations / $this->iterations;
    }

    private function generatorsFrom($supposedToBeGenerators)
    {
        $generators = [];
        foreach($supposedToBeGenerators as $supposedToBeGenerator) {
            if (!$supposedToBeGenerator instanceof Generator) {
                $generators[] = new Generator\ConstantGenerator($supposedToBeGenerator);
            } else {
                $generators[] = $supposedToBeGenerator;
            }
        }
        return $generators;
    }

    private function notifyListeners(/*$event, [$parameterA[, $parameterB[, ...]]]*/)
    {
        $arguments = func_get_args();
        $event = array_shift($arguments);
        foreach ($this->listeners as $listener) {
            call_user_func_array(
                [$listener, $event],
                $arguments
            );
        }
    }

    private function antecedentsAreSatisfied(array $values)
    {
        foreach ($this->antecedents as $antecedentToVerify) {
            if (!call_user_func(
                [$antecedentToVerify, 'evaluate'],
                $values
            )) {
                return false;
            }
        }
        return true;
    }

    private function terminationConditionsAreSatisfied()
    {
        foreach ($this->terminationConditions as $terminationCondition) {
            if ($terminationCondition->shouldTerminate()) {
                return true;
            }
        }
        return false;
    }
}
