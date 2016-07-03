<?php
namespace Eris\Listener;

use Eris\Listener;
use Exception;

abstract class EmptyListener implements Listener
{
    public function startPropertyVerification()
    {
    }

    public function endPropertyVerification($ordinaryEvaluations, $iterations, Exception $exception = null)
    {
    }

    public function newGeneration(array $generation, $iteration)
    {
    }

    public function failure(array $generation, Exception $exception)
    {
    }

    public function shrinking(array $generation)
    {
    }
}
