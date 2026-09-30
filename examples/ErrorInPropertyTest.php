<?php

use Eris\Generators;

class ErrorInPropertyTest extends \PHPUnit\Framework\TestCase
{
    use Eris\TestTrait;

    public function testErrorRaisedByTheFirstExampleIsReportedAsItself()
    {
        $this->forAll(
            Generators::int()
        )
            ->then(function ($number) {
                intdiv($number, 0);
            });
    }

    public function testErrorRaisedAfterMoreThanHalfOfTheExamplesIsReportedAsItself()
    {
        $examples = 0;

        $this->forAll(
            Generators::int()
        )
            ->then(function ($number) use (&$examples) {
                $examples++;

                if ($examples > 60) {
                    intdiv($number, 0);
                }
            });
    }
}
