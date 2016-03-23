<?php
namespace Eris\Random;

interface Source
{
    /**
     * Returns a random number between 0 and @see max().
     * @return integer
     */
    public function extractNumber();

    /**
     * @return integer
     */
    public function max();
}
