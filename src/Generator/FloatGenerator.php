<?php
namespace Eris\Generator;

use Eris\Generator;
use DomainException;

function float()
{
    return new FloatGenerator();
}

class FloatGenerator implements Generator
{
    public function __construct()
    {
    }

    public function __invoke($size, $rand)
    {
        $denominator = $rand(1, $size) ?: 1;

        $value = (float) $rand(0, $size) / (float) $denominator;

        $signedValue = $rand(0, 1) === 0
            ? $value
            : $value * (-1);
        return GeneratedValueSingle::fromJustValue($signedValue, 'float');
    }

    public function shrink(GeneratedValueSingle $element)
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
