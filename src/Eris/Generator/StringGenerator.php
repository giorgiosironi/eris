<?php
namespace Eris\Generator;

use Eris\Generator;
use DomainException;

class StringGenerator implements Generator
{
    public function __invoke($size)
    {
        $length = rand(0, $size);

        $built = '';
        for ($i = 0; $i < $length; $i++) {
            $built .= chr(rand(33, 126));
        }
        return GeneratedValue::fromJustValue($built, 'string');
    }

    public function shrink(GeneratedValue $element)
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
        return GeneratedValue::fromJustValue(
            substr($element->unbox(), 0, -1),
            'string'
        );
    }

    public function contains(GeneratedValue $element)
    {
        return is_string($element->unbox());
    }
}
