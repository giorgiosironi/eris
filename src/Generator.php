<?php
namespace Eris;

use Eris\Generator\GeneratedValue;
use Eris\Generator\GeneratedValueSingle;

/**
 * Generic interface for a type <T>.
 * @psalm-template T
 */
interface Generator
{
    /**
     * @param int The generation size
     * @param Random\RandomRange
     * @return GeneratedValueSingle<T>
     */
    public function __invoke($size, Random\RandomRange $rand);

    /**
     * The conditions for terminating are either:
     * - returning the same GeneratedValueSingle passed in
     * - returning an empty GeneratedValueOptions
     *
     * @param GeneratedValue<T>
     * @return GeneratedValue<T>
     */
    public function shrink(GeneratedValue $element);
}
