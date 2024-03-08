<?php
namespace Eris\Shrinker;

class NoTimeLimit implements TimeLimit
{
    public function start(): void
    {
    }

    public function hasBeenReached(): bool
    {
        return false;
    }

    public function __toString(): string
    {
        return 'no time limit';
    }
}
