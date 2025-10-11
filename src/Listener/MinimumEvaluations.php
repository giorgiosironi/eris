<?php
namespace Eris\Listener;

use Eris\Listener;
use Eris\Listener\EmptyListener;
use OutOfBoundsException;
use Exception;

class MinimumEvaluations extends EmptyListener implements Listener
{
    /**
     * @param float $threshold  from 0.0 to 1.0
     */
    public static function ratio($threshold): self
    {
        return new self($threshold);
    }

    private function __construct(private $threshold)
    {
    }

    public function endPropertyVerification($ordinaryEvaluations, $iterations, ?Exception $exception = null): void
    {
        if ($exception instanceof \Exception) {
            return;
        }
        $evaluationRatio = $ordinaryEvaluations / $iterations;
        if ($evaluationRatio < $this->threshold) {
            throw new OutOfBoundsException("Evaluation ratio {$evaluationRatio} is under the threshold {$this->threshold}");
        }
    }
}
