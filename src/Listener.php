<?php
namespace Eris;

use Eris\Generator\GeneratedValueSingle;
use Exception;

interface Listener
{
    /**
     * @return void
     */
    public function startPropertyVerification();

    /**
     * @param integer $ordinaryEvaluations  the number of inputs effectively evaluated, not filtered out.
     *                                      Does not count evaluations used in shrinking
     * @param integer $iterations  the total number of inputs that have been generated
     * @param null|Exception $exception  tells if the test has failed and specifies the exact exception
     * @return void
     */
    public function endPropertyVerification($ordinaryEvaluations, $iterations, Exception $exception = null);

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
