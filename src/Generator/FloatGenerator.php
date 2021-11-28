<?php
namespace Eris\Generator;

use Eris\Generator;
use Eris\Generators;
use Eris\Random\RandomRange;

function float()
{
    return Generators::float();
}

class FloatGenerator implements Generator
{
    public function __construct()
    {
    }

    public function __invoke($size, RandomRange $rand)
    {
        $denominator = $rand->rand(1, $size) ?: 1;

        $value = (float) $rand->rand(0, $size) / (float) $denominator;

        $signedValue = $rand->rand(0, 1) === 0
            ? $value
            : $value * (-1);
        return GeneratedValueSingle::fromJustValue($signedValue, 'float');
    }

    public function shrink(GeneratedValue $element)
    {
        $value = $element->unbox();

        if ($value < 0.0) {
            return GeneratedValueSingle::fromJustValue(min($value + 1.0, 0.0), 'float');
        }
        if ($value > 0.0) {
            return GeneratedValueSingle::fromJustValue(max($value - 1.0, 0.0), 'float');
        }
        return GeneratedValueSingle::fromJustValue(0.0, 'float');
    }
}
