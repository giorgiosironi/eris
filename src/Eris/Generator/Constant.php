<?php
namespace Eris\Generator;

use Eris\Generator;
use DomainException;

class Constant implements Generator
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function __invoke()
    {
        return $this->value;
    }

    public function shrink($element)
    {
        if (!$this->contains($element)) {
            throw new DomainException(
                $element . ' does not belong to the domain of the constant value ' .
                $this->value . '.'
            );
        }

        return $this->value;
    }

    public function contains($element)
    {
        return $this->value === $element;
    }
}
