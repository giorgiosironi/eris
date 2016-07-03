<?php
namespace Eris\Shrinker;

use Eris\Generator\IntegerGenerator;
use Eris\Generator\GeneratedValue;
use Eris\Generator\GeneratedValueOptions;
use PHPUnit_Framework_AssertionFailedError as AssertionFailed;

class MultipleTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->shrinker = new Multiple(
            [
                new IntegerGenerator()
            ],
            function($number) {
                $this->assertLessThanOrEqual(5000, $number);
            }
        );
    }
    
    public function testFollowsMultipleBranches()
    {
        try {
            $this->shrinker->from(
                GeneratedValue::fromValueAndInput(
                    [
                        6000
                    ],
                    [
                        GeneratedValue::fromJustValue(6000, 'integer')
                    ]
                ),
                new AssertionFailed()
            );
        } catch (AssertionFailed $e) {
            $this->assertEquals("Failed asserting that 5001 is equal to 5000 or is less than 5000", $e->getMessage());
            var_dump($e);
        }
    }
}
