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
    public function endPropertyVerification($evaluations);

    /**
     * @param array  of GeneratedValue
     * @return void
     */
    public function newGeneration(array $generatedValues);
}
