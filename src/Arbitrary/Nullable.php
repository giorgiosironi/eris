<?php

namespace Eris\Arbitrary;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class Nullable
{
    public function __construct(
        public readonly int $nullPercentage = 10,
    ) {
    }
}
