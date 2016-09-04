<?php
namespace Eris\Generator;

use Eris\Generator;
use DomainException;

function string()
{
    return new StringGenerator();
}

class StringGenerator implements Generator
{
    public function __invoke($size, $rand)
    {
        $length = $rand(0, $size);

        $built = '';
        for ($i = 0; $i < $length; $i++) {
            $built .= chr($rand(33, 126));
        }
        return GeneratedValueSingle::fromJustValue($built, 'string');
    }

    public function shrink(GeneratedValueSingle $element)
    {
        if (!$this->contains($element)) {
            throw new DomainException(
                'Cannot shrink ' . $element . ' because it does not belong ' .
                'to the domain of the Strings.'
            );
        }

        if ($element->unbox() === '') {
            return $element;
        }
        return GeneratedValueSingle::fromJustValue(
            substr($element->unbox(), 0, -1),
            'string'
        );
    }

    public function contains(GeneratedValueSingle $element)
    {
        return is_string($element->unbox());
    }
}
