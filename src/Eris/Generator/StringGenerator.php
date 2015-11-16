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
    public function __construct()
    {
    }

    public function __invoke($size)
    {
        $length = rand(0, $size);

        $built = '';
        for ($i = 0; $i < $length; $i++) {
            $built .= chr(rand(33, 126));
        }
        return $built;
    }

    public function shrink($element)
    {
        if (!$this->contains($element)) {
            throw new DomainException(
                'Cannot shrink ' . $element . ' because it does not belong ' .
                'to the domain of the Strings.'
            );
        }

        if ($element === '') {
            return '';
        }
        return substr($element, 0, -1);
    }

    public function contains($element)
    {
        return is_string($element);
    }
}
