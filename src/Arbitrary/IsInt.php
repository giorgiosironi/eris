<?php

namespace Eris\Arbitrary;

use Attribute;
use Eris\Generator;
use Eris\Generator\IntegerGenerator;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class IsInt implements GeneratorAttribute
{
    public function toGenerator(): Generator
    {
        return new IntegerGenerator();
    }
}
