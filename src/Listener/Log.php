<?php
namespace Eris\Listener;

use Eris\Listener;
use Eris\Listeners;
use Exception;

/**
 * @see Listeners::log()
 */
function log($file)
{
    return Listeners::log($file);
}

class Log extends EmptyListener implements Listener
{
    private $fp;

    public function __construct(private $file, private $time, private $pid)
    {
        $this->fp = fopen($this->file, 'w');
    }

    public function newGeneration(array $generation, $iteration): void
    {
        $this->log(sprintf(
            "iteration %d: %s",
            $iteration,
            // TODO: duplication with collect
            json_encode(
                $generation
            )
        ));
    }

    public function endPropertyVerification($ordinaryEvaluations, $iterations, ?Exception $exception = null): void
    {
        fclose($this->fp);
    }

    public function failure(array $generation, Exception $exception): void
    {
        $this->log(sprintf(
            "failure: %s. %s",
            // TODO: duplication with collect
            json_encode($generation),
            $exception->getMessage()
        ));
    }

    public function shrinking(array $generation): void
    {
        $this->log(sprintf(
            "shrinking: %s",
            // TODO: duplication with collect
            json_encode($generation)
        ));
    }

    private function log(string $text): void
    {
        fwrite(
            $this->fp,
            sprintf(
                "[%s][%s] %s" . PHP_EOL,
                date('c', call_user_func($this->time)),
                $this->pid,
                $text
            )
        );
    }
}
