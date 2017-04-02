<?php
namespace Eris\Quantifier;

use Eris\Antecedent;
use Eris\Generator;
use Eris\Generator\GeneratedValueSingle;
use Eris\Generator\SkipValueException;
use BadMethodCallException;
use PHPUnit_Framework_Constraint;
use PHPUnit\Framework\Constraint\Constraint;
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
    private $shrinkerFactoryMethod;
    private $rand;
    private $shrinkingEnabled = true;

    public function __construct(array $generators, $iterations, $shrinkerFactory, $shrinkerFactoryMethod, $rand)
    {
        $this->generators = $this->generatorsFrom($generators);
        $this->iterations = $iterations;
        $this->shrinkerFactory = $shrinkerFactory;
        $this->shrinkerFactoryMethod = $shrinkerFactoryMethod;
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
        } elseif ($arguments[0] instanceof PHPUnit_Framework_Constraint) {
            $antecedent = Antecedent\IndependentConstraintsAntecedent::fromAll($arguments);
        } elseif ($arguments[0] instanceof Constraint) {
            $antecedent = Antecedent\IndependentConstraintsAntecedent::fromAll($arguments);
        } elseif ($arguments && count($arguments) == 1) {
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
            $redTestException = null;
            $this->notifyListeners('startPropertyVerification');
            for (
                $iteration = 0;
                $iteration < $this->iterations
                && !$this->terminationConditionsAreSatisfied();
                $iteration++
            ) {
                $generatedValues = [];
                $values = [];
                try {
                    foreach ($this->generators as $name => $generator) {
                        $value = $generator($sizes->at($iteration), $this->rand);
                        if (!($value instanceof GeneratedValueSingle)) {
                            throw new RuntimeException("The value returned by a generator should be an instance of GeneratedValueSingle, but it is " . var_export($value, true));
                        }
                        $generatedValues[] = $value;
                        $values[] = $value->unbox();
                    }
                } catch (SkipValueException $e) {
                    continue;
                }
                $generation = GeneratedValueSingle::fromValueAndInput(
                    $values,
                    $generatedValues,
                    'tuple'
                );
                $this->notifyListeners('newGeneration', $generation->unbox(), $iteration);

                if (!$this->antecedentsAreSatisfied($values)) {
                    continue;
                }

                $this->ordinaryEvaluations++;
                Evaluation::of($assertion)
                    // TODO: coupling between here and the TupleGenerator used inside?
                    ->with($generation)
                    ->onFailure(function ($generatedValues, $exception) use ($assertion) {
                        $this->notifyListeners('failure', $generatedValues->unbox(), $exception);
                        if (!$this->shrinkingEnabled) {
                            throw $exception;
                        }
                        $shrinkerFactoryMethod = $this->shrinkerFactoryMethod;
                        $shrinking = $this->shrinkerFactory->$shrinkerFactoryMethod($this->generators, $assertion);
                        // MAYBE: put into ShrinkerFactory?
                        $shrinking
                            ->addGoodShrinkCondition(function (GeneratedValueSingle $generatedValues) {
                                return $this->antecedentsAreSatisfied($generatedValues->unbox());
                            })
                            ->onAttempt(function (GeneratedValueSingle $generatedValues) {
                                $this->notifyListeners('shrinking', $generatedValues->unbox());
                            })
                            ->from($generatedValues, $exception);
                    })
                    ->execute();
            }
        } catch (Exception $e) {
            $redTestException = $e;
            $wrap = (bool) getenv('ERIS_ORIGINAL_INPUT');
            if ($wrap) {
                $message = "Original input: " . var_export($values, true) . PHP_EOL
                    . "Possibly shrinked input follows." . PHP_EOL;
                throw new RuntimeException($message, -1, $e);
            } else {
                throw $e;
            }
        } finally {
            $this->notifyListeners(
                'endPropertyVerification',
                $this->ordinaryEvaluations,
                $this->iterations,
                $redTestException
            );
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

    private function generatorsFrom($supposedToBeGenerators)
    {
        $generators = [];
        foreach ($supposedToBeGenerators as $supposedToBeGenerator) {
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
