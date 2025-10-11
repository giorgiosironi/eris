<?php

namespace Eris\Random;

class MtRandSource implements Source
{
    /**
     * Returns a random number between 0 and @see max().
     */
    public function extractNumber(): int
    {
        return mt_rand(0, $this->max());
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
