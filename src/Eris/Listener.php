<?php
namespace Eris;

use Eris\Generator\GeneratedValue;
use Exception;

interface Listener
{
    /**
     * @return void
     */
    public function startPropertyVerification();

    /**
     * @param integer $ordinaryEvaluations
     * @return void
     */
    public function endPropertyVerification($ordinaryEvaluations);

    /**
     * @param array $generation  of mixed values
     * @param integer $iteration  index of current iteration
     * @return void
     */
    public function newGeneration(array $generation, $iteration);

    /**
     * @param array $generation  of mixed values
     * @param Exception $exception  assertion failure
     * @return void
     */
    public function failure(array $generation, Exception $exception);

    /**
     * @param array $generation  of mixed values
     * @return void
     */
    public function shrinking(array $generation);
}
