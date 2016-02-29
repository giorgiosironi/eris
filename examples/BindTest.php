<?php
use Eris\Generator;

class BindTest extends PHPUnit_Framework_TestCase
{
    use Eris\TestTrait;

    public function testApplyingAFunctionToGeneratedValues()
    {
        $sequenceGenerator = Generator\seq(Generator\nat());
        $this->forAll(
            Generator\bind(
                $sequenceGenerator,
                function($sequence) {
                    return Generator\tuple(
                        Generator\elements($sequence),
                        Generator\constant($sequence)
                    );
                }
            )
        )
            ->then(function($tuple) {
                list ($element, $sequence) = $tuple;
                $this->assertContains($element, $sequence);
            });
    }
}
