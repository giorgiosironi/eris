<?php

namespace Eris\Arbitrary\Fixtures;

class Order
{
    public function __construct(
        public readonly string $product,
        public readonly int $quantity,
        public readonly float $price,
    ) {
    }
}
