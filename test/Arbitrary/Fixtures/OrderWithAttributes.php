<?php

namespace Eris\Arbitrary\Fixtures;

use Eris\Arbitrary\Choose;

class OrderWithAttributes
{
    public function __construct(
        public readonly string $product,
        #[Choose(1, 10)]
        public readonly int $quantity,
    ) {
    }
}
