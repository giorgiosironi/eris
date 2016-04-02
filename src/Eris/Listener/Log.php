<?php
namespace Eris\Listener;

use Eris\Listener;

function log($file)
{
    return new Log($file, 'time', getmypid());
}

class Log
    extends EmptyListener
    implements Listener
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

    public function newGeneration(array $generatedValues, $iteration)
    {
        $this->log("iteration $iteration");
    }

    public function endPropertyVerification($ordinaryEvaluations)
    {
        fclose($this->fp);
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
