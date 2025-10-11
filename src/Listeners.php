<?php

namespace Eris;

use Eris\Listener\CollectFrequencies;
use Eris\Listener\Log;

final class Listeners
{
    public static function collectFrequencies(?callable $collectFunction = null): \Eris\Listener\CollectFrequencies
    {
        return new CollectFrequencies($collectFunction);
    }

    public static function log($file): \Eris\Listener\Log
    {
        return new Log($file, 'time', getmypid());
    }
}
