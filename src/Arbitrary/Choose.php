<?php

namespace Eris\Arbitrary;

use Attribute;
use Eris\Generator;
use Eris\Generator\ChooseGenerator;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class Choose implements GeneratorAttribute
{
    public function __construct(
        private readonly int $lower,
        private readonly int $upper,
    ) {
    }

    public function toGenerator(): Generator
    {
        return new ChooseGenerator($this->lower, $this->upper);
    }
}
