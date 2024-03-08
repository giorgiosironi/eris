<?php
namespace Eris\Shrinker;

interface TimeLimit
{
    /**
     * Call to start measuring the time interval.
     */
    public function start(): void;
    public function hasBeenReached(): bool;
    public function __toString(): string;
}
