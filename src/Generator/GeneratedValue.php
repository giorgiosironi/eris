<?php
namespace Eris\Generator;

use Countable;
use IteratorAggregate;

/**
 * @template T
 */
interface GeneratedValue extends IteratorAggregate, Countable
{
    /**
     * @param callable $applyToValue
     * @param string $generatorName
     * @return GeneratedValue
     */
    public function map(callable $applyToValue, $generatorName);

    /**
     * @return mixed
     */
    public function input();

    /**
     * @psalm-return T
     * @return mixed
     */
    public function unbox();
}
