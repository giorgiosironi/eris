<?php
namespace Eris;
use OutOfBoundsException;

trait TestTrait
{
    // TODO: make this private as much as possible
    private $quantifiers = [];
    protected $iterations = 100;
    // TODO: what is the correct name for this concept?
    protected $minimumEvaluationRatio = 0.5;
    protected $seed;
    protected $shrinkingTimeLimit = null;

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
        $quantifier = new Quantifier\ForAll(
            $generators,
            $this->iterations,
            new Shrinker\ShrinkerFactory([
                'timeLimit' => $this->shrinkingTimeLimit,
            ])
        );
        $this->quantifiers[] = $quantifier;
        return $quantifier;
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
