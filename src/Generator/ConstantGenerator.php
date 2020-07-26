<?php
namespace Eris\Generator;

use Eris\Generator;
use Eris\Random\RandomRange;

/**
 * @param mixed $value  the only value to generate
 * @return ConstantGenerator
 */
function constant($value)
{
    return ConstantGenerator::constant($value);
}

class ConstantGenerator implements Generator
{
    private $value;

    public static function box($value)
    {
        return new self($value);
    }

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

    /**
     * @param mixed $value  the only value to generate
     * @return ConstantGenerator
     */
    public static function constant($value)
    {
        return self::box($value);
    }
}
