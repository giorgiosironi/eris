<?php

namespace Eris\Arbitrary\Fixtures;

class OrderWithNullable
{
    public function __construct(
        public readonly string $product,
        public readonly ?int $quantity,
    ) {
    }
}
