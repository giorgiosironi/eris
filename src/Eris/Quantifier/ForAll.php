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

    public function suchThat(callable $antecedent)
    {
        $this->antecedents[] = $antecedent; 
        return $this;
    }

    public function __invoke($assertion)
    {
        for ($i = 0; $i < $this->iterations; $i++) {
            $values = [];
            foreach ($this->generators as $name => $generator) {
                $values[] = $generator();
            }
            foreach ($this->antecedents as $antecedentToVerify) {
                if (!call_user_func_array($antecedentToVerify, $values)) {
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
