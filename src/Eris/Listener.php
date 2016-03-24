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
     * @param integer
     * @return void
     */
    public function endPropertyVerification($ordinaryEvaluations);

    /**
     * @param array  of mixed values
     * @param integer  index of current iteration
     * @return void
     */
    public function newGeneration(array $generation, $iteration);

    /**
     * @param array  of mixed values
     * @param Exception  assertion failure
     * @return void
     */
    public function failure(array $generation, Exception $exception);

    /**
     * @param array  of mixed values
     * @return void
     */
    public function shrinking(array $generation);
}
