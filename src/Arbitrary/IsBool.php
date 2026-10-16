<?php

namespace Eris\Arbitrary;

use Attribute;
use Eris\Generator;
use Eris\Generator\BooleanGenerator;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class IsBool implements GeneratorAttribute
{
    public function toGenerator(): Generator
    {
        return new BooleanGenerator();
    }
}
