<?php
namespace Eris\Generator;

use Countable;
use IteratorAggregate;

/**
 * @psalm-template T
 * @template-extends IteratorAggregate<integer,T>
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
     * @return mixed
     */
    public function unbox();
}
