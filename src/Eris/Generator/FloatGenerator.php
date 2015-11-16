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

    public function __invoke($size)
    {
        $value = (float) rand(0, $size) / (float) rand(1, $size);

        return rand(0, 1) === 0
                          ? $value
                          : $value * (-1);
    }

    public function shrink($element)
    {
        if (!$this->contains($element)) {
            throw new DomainException(
                'Cannot shrink ' . $element . ' because it does not belong ' .
                'to the domain of Floats'
            );
        }

        if ($element < 0.0) {
            return min($element + 1.0, 0.0);
        }
        if ($element > 0.0) {
            return max($element - 1.0, 0.0);
        }
        return 0.0;
    }

    public function contains($element)
    {
        return is_float($element);
    }
}
