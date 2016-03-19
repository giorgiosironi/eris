<?php
namespace Eris;

use Eris\Generator\GeneratedValue;

interface Listener
{
    /**
     * @return void
     */
    public function startPropertyVerification();

    /**
     * @param integer
     * @return void
     */
    public function endPropertyVerification($ordinaryEvaluations);

    /**
     * @param array  of GeneratedValue
     * @param integer  index of current iteration
     * @return void
     */
    public function newGeneration(array $generatedValues, $iteration);
}
