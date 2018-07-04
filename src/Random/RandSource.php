<?php

namespace Eris\Random;

class RandSource implements Source
{
    /**
     * Returns a random number between 0 and @see max().
     * @return integer
     */
    public function extractNumber()
    {
        return rand(0, $this->max());
    }

    /**
     * @return integer
     */
    public function max()
    {
        return getrandmax();
    }

    /**
     * @param integer $seed
     * @return self
     */
    public function seed($seed)
    {
        srand($seed);
        return $this;
    }
}
