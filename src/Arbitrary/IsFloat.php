<?php

namespace Eris\Arbitrary;

use Attribute;
use Eris\Generator;
use Eris\Generator\FloatGenerator;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class IsFloat implements GeneratorAttribute
{
    public function toGenerator(): Generator
    {
        return new FloatGenerator();
    }
}
