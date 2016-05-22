<?php
namespace Eris\Listener;

use Eris\Listener;
use Eris\Listener\EmptyListener;
use OutOfBoundsException;
use Exception;

class MinimumEvaluationRatio
    extends EmptyListener
    implements Listener
{
    private $threshold;
    
    public function __construct($threshold)
    {
        $this->threshold = $threshold;
    }

    public function endPropertyVerification($ordinaryEvaluations, $iterations, Exception $exception = null)
    {
        if ($exception) {
            return;
        }
        $evaluationRatio = $ordinaryEvaluations / $iterations;
        if ($evaluationRatio < $this->threshold) {
            throw new OutOfBoundsException("Evaluation ratio {$evaluationRatio} is under the threshold {$this->threshold}");
        }
    }    
}
