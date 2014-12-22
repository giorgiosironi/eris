<?php
namespace Eris\Shrinker;

class NoTimeLimit implements TimeLimit
{
    public function start()
    {
        
    }

    public function hasBeenReached()
    {
        return false;
    }
}
