<?php

namespace Eris\Quantifier;

use Eris\Antecedent;
use Eris\Generator;
use Eris\Generator\GeneratedValueSingle;
use Eris\Generator\SkipValueException;
use BadMethodCallException;
use PHPUnit\Framework\Constraint\Constraint;
use Exception;
use RuntimeException;
use Eris\Listener;
use Eris\Random\RandomRange;

/**
 * @method self and()
 */
class ForAll
{
    const DEFAULT_MAX_SIZE = 200;

    private $generators;
    private $maxSize = self::DEFAULT_MAX_SIZE;
    private array $antecedents = [];
    private int $ordinaryEvaluations = 0;
    private array $aliases = [
        'and' => 'when',
    ];
    private array $terminationConditions = [];
    private array $listeners = [];
    private bool $shrinkingEnabled = true;

    public function __construct(array $generators, private $iterations, private $shrinkerFactory, private $shrinkerFactoryMethod, private readonly RandomRange $rand)
    {
        $this->generators = $this->generatorsFrom($generators);
    }

    /**
     * @param integer $maxSize
     */
    public function withMaxSize($maxSize): static
    {
        $this->maxSize = $maxSize;
        return $this;
    }

    public function hook(Listener $listener): static
    {
        $this->listeners[] = $listener;
        return $this;
    }

    public function stopOn(TerminationCondition $terminationCondition): static
    {
        $this->terminationConditions[] = $terminationCondition;
        return $this;
    }

    public function disableShrinking(): static
    {
        $this->shrinkingEnabled = false;
        return $this;
    }

    /**
     * Examples of calls:
     * when($constraint1, $constraint2, ..., $constraintN)
     * when(callable $takesNArguments)
     * when(Antecedent $antecedent)
     */
    public function when(/* see docblock */): static
    {
        $arguments = func_get_args();
        if ($arguments[0] instanceof Antecedent) {
            $antecedent = $arguments[0];
        } elseif ($arguments[0] instanceof Constraint) {
            $antecedent = Antecedent\IndependentConstraintsAntecedent::fromAll($arguments);
        } elseif ($arguments && count($arguments) === 1) {
            $antecedent = Antecedent\SingleCallbackAntecedent::from($arguments[0]);
        } else {
            throw new \InvalidArgumentException("Invalid call to when(): " . var_export($arguments, true));
        }
        $this->antecedents[] = $antecedent;
        return $this;
    }

    public function __invoke(callable $assertion): void
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
                    foreach ($this->generators as $generator) {
                        $value = $generator($sizes->at($iteration), $this->rand);
                        if (!($value instanceof GeneratedValueSingle)) {
                            throw new RuntimeException("The value returned by a generator should be an instance of GeneratedValueSingle, but it is " . var_export($value, true));
                        }
                        $generatedValues[] = $value;
                        $values[] = $value->unbox();
                    }
                } catch (SkipValueException) {
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
                    ->onFailure(function ($generatedValues, $exception) use ($assertion): void {
                        $this->notifyListeners('failure', $generatedValues->unbox(), $exception);
                        if (!$this->shrinkingEnabled) {
                            throw $exception;
                        }
                        $shrinkerFactoryMethod = $this->shrinkerFactoryMethod;
                        $shrinking = $this->shrinkerFactory->$shrinkerFactoryMethod($this->generators, $assertion);
                        // MAYBE: put into ShrinkerFactory?
                        $shrinking
                            ->addGoodShrinkCondition(fn(GeneratedValueSingle $generatedValues) => $this->antecedentsAreSatisfied($generatedValues->unbox()))
                            ->onAttempt(function (GeneratedValueSingle $generatedValues): void {
                                $this->notifyListeners('shrinking', $generatedValues->unbox());
                            })
                            ->from($generatedValues, $exception);
                    })
                    ->execute();
            }
        } catch (Exception $e) {
            $redTestException = $e;
            if ((bool) getenv('ERIS_ORIGINAL_INPUT')) {
                $message = "Original input: " . var_export($values, true) . PHP_EOL
                    . "Possibly shrinked input follows." . PHP_EOL;
                throw new RuntimeException($message, -1, $e);
            }
            throw $e;
        } finally {
            $this->notifyListeners(
                'endPropertyVerification',
                $this->ordinaryEvaluations,
                $this->iterations,
                $redTestException
            );
        }
    }
    
    public function then(callable $assertion): void
    {
        $this->__invoke($assertion);
    }

    /**
     * @see $this->aliases
     * @method then($assertion)
     * @method implies($assertion)
     * @method imply($assertion)
     */
    public function __call(string $method, array $arguments)
    {
        if (isset($this->aliases[$method])) {
            return call_user_func_array(
                [$this, $this->aliases[$method]],
                $arguments
            );
        }
        throw new BadMethodCallException("Method " . self::class . "::{$method} does not exist");
    }

    /**
     * @return list<\Eris\Generator>
     */
    private function generatorsFrom(array $supposedToBeGenerators): array
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

    private function notifyListeners(/*$event, [$parameterA[, $parameterB[, ...]]]*/): void
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

    private function antecedentsAreSatisfied(array $values): bool
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

    private function terminationConditionsAreSatisfied(): bool
    {
        foreach ($this->terminationConditions as $terminationCondition) {
            if ($terminationCondition->shouldTerminate()) {
                return true;
            }
        }
        return false;
    }
}
