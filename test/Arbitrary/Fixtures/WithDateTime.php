<?php

namespace Eris\Arbitrary\Fixtures;

use Eris\Arbitrary\Generate;

#[Generate]
class WithDateTime
{
    public \DateTime $date;
}
