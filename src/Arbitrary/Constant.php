<?php

namespace Eris\Arbitrary;

use Attribute;
use Eris\Generator;
use Eris\Generator\ConstantGenerator;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class Constant implements GeneratorAttribute
{
    public function __construct(
        private readonly mixed $value,
    ) {
    }

    public function toGenerator(): Generator
    {
        return ConstantGenerator::box($this->value);
    }
}
