<?php
namespace Eris\Quantifier;

class ForAll
{
    private $generators;
    private $iterations;
    private $antecedents = [];
    
    public function __construct(array $generators, $iterations)
    {
        $this->generators = $generators;
        $this->iterations = $iterations;
    }

    /**
     * Examples of calls:
     * suchThat($matcher1, $matcher2, ..., $matcherN)
     * suchThat(callable $takesNArguments)
     * @return self
     */
    public function suchThat(/* see docblock */)
    {
        $arguments = func_get_args();
        if ($arguments[0] instanceof \PHPUnit_Framework_Constraint) {
            $antecedent = Antecedent\IndependentConstraintsAntecedent::fromAll($arguments);
        } else if ($arguments && count($arguments) == 1) {
            $antecedent = Antecedent\SingleCallbackAntecedent::from($arguments[0]);
        } else {
            throw new \InvalidArgumentException("Invalid call to suchThat: " . var_export($arguments, true));
        }
        $this->antecedents[] = $antecedent; 
        return $this;
    }

    public function __invoke($assertion)
    {
        for ($i = 0; $i < $this->iterations; $i++) {
            $values = [];
            foreach ($this->generators as $name => $generator) {
                $value = $generator();
                $values[] = $value;
            }
            foreach ($this->antecedents as $antecedentToVerify) {
                if (!call_user_func(
                    [$antecedentToVerify, 'evaluate'],
                    $values
                )) {
                    continue 2;
                }
            }
            Evaluation::of($assertion)
                ->with($values)
                ->onFailure(function($values, $exception) use ($assertion) {
                    $shrinking = new RoundRobinShrinking($this->generators, $assertion);
                    $shrinking->from($values, $exception);
                })
                ->execute();
        }
    }
}
