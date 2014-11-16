<?php
namespace Eris;

/**
 * TODO: rename to Generator once inside a Brioche\ namespace.
 * PHP already has a Generator class inside the root namespace which clashes with this.
 *
 * Generic interface for a type <T>.
 */
interface Generator
{
    /**
     * @return T
     */
    public function __invoke();

    /**
     * @return T
     */
    public function shrink();
}
