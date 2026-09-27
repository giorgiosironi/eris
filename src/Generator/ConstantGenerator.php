<?php
namespace Eris\Generator;

use Eris\Generator;
use Eris\Generators;
use Eris\Random\RandomRange;

/**
 * @template T
 * @param T $value  the only value to generate
 * @return ConstantGenerator<T>
 */
function constant($value)
{
    return Generators::constant($value);
}

/**
 * @template-covariant T
 * @template-implements Generator<T>
 */
class ConstantGenerator implements Generator
{
    private $value;

    /**
     * @template U
     * @param U $value
     * @return self<U>
     */
    public static function box($value)
    {
        return new self($value);
    }

    /**
     * @param T $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    public function __invoke($_size, RandomRange $rand)
    {
        return GeneratedValueSingle::fromJustValue($this->value, 'constant');
    }

    public function shrink(GeneratedValue $element)
    {
        return GeneratedValueSingle::fromJustValue($this->value, 'constant');
    }
}
