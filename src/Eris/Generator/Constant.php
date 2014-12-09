<?php
namespace Eris\Generator;
use Eris\Generator;

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
        return $this->value;
    }

    public function contains($element)
    {
        return $this->value === $element;
    }
}
