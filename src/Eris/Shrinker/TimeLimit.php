<?php
namespace Eris\Shrinker;

interface TimeLimit
{
    /**
     * Call to start measuring the time interval.
     *
     * @return void
     */
    public function start();

    /**
     * @return bool
     */
    public function hasBeenReached();

    /**
     * @return string
     */
    public function __toString();
}
