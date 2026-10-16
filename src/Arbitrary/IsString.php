<?php

namespace Eris\Arbitrary;

use Attribute;
use Eris\Generator;
use Eris\Generator\StringGenerator;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class IsString implements GeneratorAttribute
{
    public function toGenerator(): Generator
    {
        return new StringGenerator();
    }
}
