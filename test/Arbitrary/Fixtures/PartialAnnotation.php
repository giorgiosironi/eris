<?php

namespace Eris\Arbitrary\Fixtures;

use Eris\Arbitrary\Choose;

class PartialAnnotation
{
    #[Choose(1, 100)]
    public int $annotated;

    public string $notAnnotated;
}
