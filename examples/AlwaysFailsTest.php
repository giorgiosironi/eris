<?php

use Eris\Generators;
use Eris\TestTrait;

class AlwaysFailsTest extends \PHPUnit\Framework\TestCase
{
    use TestTrait;

    public function testFailsNoMatterWhatIsTheInput()
    {
        $this->forAll(
            Generators::elements(['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j'])
        )
            ->then(function ($someChar) {
                $this->fail("This test fails by design. '$someChar' was passed in");
            });
    }
}
