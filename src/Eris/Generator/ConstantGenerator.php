<?php
namespace Eris\Generator;

use Eris\Generator;
use DomainException;

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

    public function __invoke($_size)
    {
        return GeneratedValue::fromJustValue($this->value, 'constant');
    }

    public function shrink($element)
    {
        if (!$this->contains($element)) {
            throw new DomainException(
                $element . ' does not belong to the domain of the constant value ' .
                $this->value . '.'
            );
        }

        return GeneratedValue::fromJustValue($this->value, 'constant');
    }

    public function contains($element)
    {
        return $this->value === $element->input();
    }
}
