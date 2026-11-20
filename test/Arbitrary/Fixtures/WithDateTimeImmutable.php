<?php

namespace Eris\Arbitrary\Fixtures;

use Eris\Arbitrary\Generate;

#[Generate]
class WithDateTimeImmutable
{
    public \DateTimeImmutable $date;
}
