<?php
namespace Eris\Generator;

use Eris\Generator;
use DomainException;

function float()
{
    return new Float();
}

class Float implements Generator
{
    public function __construct()
    {
    }

    public function __invoke($size)
    {
        return (float) rand(0, $size) / (float) rand(1, $size);
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
