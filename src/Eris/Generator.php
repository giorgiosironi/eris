<?php
namespace Eris;

/**
 * Generic interface for a type <T>.
 */
interface Generator
{
    /**
     * @params int The generation size
     * @return T
     */
    public function __invoke($size);

    /**
     * @param T
     * @return T
     */
    public function shrink($element);

    /**
     * @param mixed
     * @return boolean
     */
    public function contains($element);
}
