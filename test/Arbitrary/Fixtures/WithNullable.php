<?php

namespace Eris\Arbitrary\Fixtures;

use Eris\Arbitrary\Generate;
use Eris\Arbitrary\Nullable;

#[Generate]
class WithNullable
{
    public ?string $maybeName;

    #[Nullable(50)]
    public ?int $maybeCount;
}
