<?php

namespace Eris\Random;

class RandSource implements Source
{
    /**
     * Returns a random number between 0 and @see max().
     */
    public function extractNumber(): int
    {
        return random_int(0, $this->max());
    }

    public function max(): int
    {
        return mt_getrandmax();
    }

    /**
     * @param integer $seed
     */
    public function seed($seed): static
    {
        mt_srand($seed);
        return $this;
    }
}
