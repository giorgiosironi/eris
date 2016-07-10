<?php
namespace Eris;

use Eris\Generator\GeneratedValue;

/**
 * Generic interface for a type <T>.
 */
interface Generator
{
    /**
     * @param int The generation size
     * @param callable  a rand() function
     * @return GeneratedValue<T>
     */
    public function __invoke($size, $rand);

    /**
     * The conditions for terminating are either:
     * - returning the same GeneratedValue passed in
     * - returning an empty GeneratedValueOptions
     *
     * @param GeneratedValue<T>
     * @return GeneratedValue<T>|GeneratedValueOptions<T>
     */
    public function shrink(GeneratedValue $element);

    /**
     * @param GeneratedValue
     * @return boolean
     */
    public function contains(GeneratedValue $element);
}
