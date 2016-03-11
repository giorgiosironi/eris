<?php
namespace Eris\Listener;

use Eris\Listener;
use Eris\Listener\EmptyListener;

function log($file)
{
    return new Log($file, 'time', getmypid());
}

class Log
    extends EmptyListener
    implements Listener
{
    private $file;
    private $time;
    private $pid;

    public function __construct($file, $time, $pid)
    {
        if (file_exists($file)) {
            unlink($file);
        }
        $this->file = $file;
        $this->time = $time;
        $this->pid = $pid;
    }

    public function newGeneration(array $generatedValues, $iteration)
    {
        $this->log("iteration $iteration");
    }

    private function log($text)
    {
        file_put_contents(
            $this->file,
            sprintf(
                "[%s][%s] %s" . PHP_EOL,
                date('c', call_user_func($this->time)),
                $this->pid,
                $text
            ),
            FILE_APPEND
        );
    }
}
