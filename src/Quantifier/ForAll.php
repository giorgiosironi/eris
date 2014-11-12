<?php
namespace Quantifier;

class ForAll
{
    private $generators;
    private $iterations;
    
    public function __construct(array $generators, $iterations)
    {
        $this->generators = $generators;
        $this->iterations = $iterations;
    }

    public function __invoke($assertion)
    {
        for ($i = 0; $i < $this->iterations; $i++) {
            $values = [];
            foreach ($this->generators as $name => $generator) {
                $values[] = $generator();
            }
            Evaluation::of($assertion)
                ->with($values)
                ->onFailure(function() use ($assertion, $values) {
                    $shrinking = new Shrinking($this->generators, $assertion);
                    $shrinking->from($values);
                })
                ->execute();
        }
    }
}
