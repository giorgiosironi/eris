<?php
namespace Eris\Listener;

use Eris\Listener;

abstract class EmptyListener implements Listener
{
    public function startPropertyVerification()
    {
    }

    public function endPropertyVerification($ordinaryEvaluations)
    {
    }

    public function newGeneration(array $generatedValues, $iteration)
    {
    }
}
