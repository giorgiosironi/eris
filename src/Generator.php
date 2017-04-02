<?php
namespace Eris;

use Eris\Generator\GeneratedValueSingle;

/**
 * Generic interface for a type <T>.
 */
interface Generator
{
    /**
     * @param int The generation size
     * @param callable  a rand() function
     * @return GeneratedValueSingle<T>
     */
    public function __invoke($size, $rand);

    /**
     * The conditions for terminating are either:
     * - returning the same GeneratedValueSingle passed in
     * - returning an empty GeneratedValueOptions
     *
     * @param GeneratedValueSingle<T>
     * @return GeneratedValueSingle<T>|GeneratedValueOptions<T>
     */
    public function shrink(GeneratedValueSingle $element);
}
