<?php
namespace Eris;

/**
 * Generic interface for a type <T>.
 */
interface Generator
{
    /**
     * @return T
     */
    public function __invoke();

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
