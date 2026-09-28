<?php

namespace Eris\Arbitrary\Fixtures;

use Eris\Arbitrary\Choose;
use Eris\Arbitrary\Generate;

#[Generate]
class LineItem
{
    public function __construct(
        public readonly string $sku,
        #[Choose(1, 5)]
        public readonly int $quantity,
    ) {
    }
}
