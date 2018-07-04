<?php

namespace Eris\Random;

class MtRandSource implements Source
{
    /**
     * Returns a random number between 0 and @see max().
     * @return integer
     */
    public function extractNumber()
    {
        return mt_rand(0, $this->max());
    }

    /**
     * @return integer
     */
    public function max()
    {
        return mt_getrandmax();
    }

    /**
     * @param integer $seed
     * @return self
     */
    public function seed($seed)
    {
        mt_srand($seed);
        return $this;
    }
}
