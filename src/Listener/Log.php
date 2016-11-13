<?php
namespace Eris\Listener;

use Eris\Listener;
use Eris\Listener\EmptyListener;
use Exception;

function log($file)
{
    return new Log($file, 'time', getmypid());
}

class Log extends EmptyListener implements Listener
{
    private $file;
    private $fp;
    private $time;
    private $pid;

    public function __construct($file, $time, $pid)
    {
        $this->file = $file;
        $this->fp = fopen($file, 'w');
        $this->time = $time;
        $this->pid = $pid;
    }

    public function newGeneration(array $generation, $iteration)
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

    public function endPropertyVerification($ordinaryEvaluations, $iterations, Exception $exception = null)
    {
        fclose($this->fp);
    }

    public function failure(array $generation, Exception $exception)
    {
        $this->log(sprintf(
            "failure: %s. %s",
            // TODO: duplication with collect
            json_encode($generation),
            $exception->getMessage()
        ));
    }

    public function shrinking(array $generation)
    {
        $this->log(sprintf(
            "shrinking: %s",
            // TODO: duplication with collect
            json_encode($generation)
        ));
    }

    private function log($text)
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
