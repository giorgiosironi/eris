<?php
namespace Eris;

use Eris\Generator\GeneratedValue;
use Eris\Generator\GeneratedValueSingle;

/**
 * Generic interface for a type <T>.
 *
 * @template T
 */
interface Generator
{
    /**
     * @param int $size The generation size
     * @param Random\RandomRange $rand
     * @return GeneratedValueSingle<T>
     */
    public function __invoke($size, Random\RandomRange $rand);

    /**
     * The conditions for terminating are either:
     * - returning the same GeneratedValueSingle passed in
     * - returning an empty GeneratedValueOptions
     *
     * @param GeneratedValue<T> $element
     * @return GeneratedValue<T>
     */
    public function shrink(GeneratedValue $element);
}
