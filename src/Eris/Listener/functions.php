<?php

namespace Eris\Listener;

function collectFrequencies(callable $collectFunction = null)
{
    return new CollectFrequencies($collectFunction);
}

function log($file)
{
    return new Log($file, 'time', getmypid());
}
