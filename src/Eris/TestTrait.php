<?php
namespace Eris;
use OutOfBoundsException;

trait TestTrait
{
    private $quantifiers = [];
    protected $iterations = 100;
    // TODO: what is the correct name for this concept?
    protected $minimumEvaluationRatio = 0.5;

    /**
     * PHPUnit 4.x-only feature. If you want to use it in 3.x, call this
     * in your tearDown() method.
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

    protected function forAll($generators)
    {
        $quantifier = new Quantifier\ForAll($generators, $this->iterations, $this);
        $this->quantifiers[] = $quantifier;
        return $quantifier;
    }

    protected function genNat()
    {
        return new Generator\Natural(0, $this->iterations * 10);
    }

    protected function genOneOf(/*$a, $b, ...*/)
    {
        $arguments = func_get_args();
        if (count($arguments) == 1) {
            return Generator\OneOf::fromArray($arguments[0]);
        } else {
            return Generator\OneOf::fromArray($arguments);
        }
    }

    protected function sample(Generator $generator, $size = 10)
    {
        return Sample::of($generator)->withSize($size);
    }

    protected function sampleShrink(Generator $generator)
    {
        return Sample::of($generator)->shrink();
    }
}
