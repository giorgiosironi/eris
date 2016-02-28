<?php
namespace Eris;

use Eris\Generator\GeneratedValue;

/**
 * Generic interface for a type <T>.
 */
interface Generator
{
    /**
     * @params int The generation size
     * @return GeneratedValue<T>
     */
    public function __invoke($size);

    /**
     * @param GeneratedValue<T>
     * @return GeneratedValue<T>
     */
    public function shrink(GeneratedValue $element);

    /**
     * @param GeneratedValue
     * @return boolean
     */
    public function contains(GeneratedValue $element);
}
