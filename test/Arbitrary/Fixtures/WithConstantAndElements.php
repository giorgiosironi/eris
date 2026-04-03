<?php

namespace Eris\Arbitrary\Fixtures;

use Eris\Arbitrary\Constant;
use Eris\Arbitrary\Elements;
use Eris\Arbitrary\Generate;

#[Generate]
class WithConstantAndElements
{
    #[Constant('fixed')]
    public string $fixed;

    #[Elements('a', 'b', 'c')]
    public string $choice;
}
