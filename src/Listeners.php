<?php

namespace Eris;

use Eris\Listener\CollectFrequencies;
use Eris\Listener\Log;

final class Listeners
{
    public static function collectFrequencies(callable $collectFunction = null)
    {
        return new CollectFrequencies($collectFunction);
    }

    public static function log($file)
    {
        return new Log($file, 'time', getmypid());
    }
}
