<?php
namespace Eris\Generator;

use Countable;
use IteratorAggregate;

/**
 * @template-covariant T
 * @template-extends IteratorAggregate<int, GeneratedValueSingle<T>>
 */
interface GeneratedValue extends IteratorAggregate, Countable
{
    /**
     * T in a parameter of the callable is a covariant position,
     * which Psalm does not recognise (PHPStan does).
     *
     * @template U
     * @param callable(T): U $applyToValue
     * @param string $generatorName
     * @return GeneratedValue<U>
     * @psalm-suppress InvalidTemplateParam
     */
    public function map(callable $applyToValue, $generatorName);

    /**
     * @return mixed
     */
    public function input();

    /**
     * @return T
     */
    public function unbox();
}
