<?php

namespace Eris\Arbitrary\Fixtures;

use Eris\Arbitrary\ArrayOf;
use Eris\Arbitrary\Generate;

#[Generate]
class WithArrays
{
    #[ArrayOf('int')]
    public array $numbers;

    #[ArrayOf('string')]
    public array $names;

    #[ArrayOf('int', min: 2, max: 5)]
    public array $bounded;
}
