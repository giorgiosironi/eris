<?php

namespace Eris\Arbitrary;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class ArrayOf
{
    public function __construct(
        public readonly string $type,
        public readonly ?int $min = null,
        public readonly ?int $max = null,
    ) {
    }
}
