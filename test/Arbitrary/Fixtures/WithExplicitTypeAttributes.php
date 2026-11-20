<?php

namespace Eris\Arbitrary\Fixtures;

use Eris\Arbitrary\Generate;
use Eris\Arbitrary\IsBool;
use Eris\Arbitrary\IsFloat;
use Eris\Arbitrary\IsInt;
use Eris\Arbitrary\IsString;

#[Generate]
class WithExplicitTypeAttributes
{
    #[IsString]
    public string $name;

    #[IsInt]
    public int $count;

    #[IsFloat]
    public float $ratio;

    #[IsBool]
    public bool $active;
}
