<?php
namespace Eris\Shrinker;

use Eris\Generator\IntegerGenerator;
use Eris\Generator\GeneratedValueSingle;
use Eris\Generator\GeneratedValueOptions;
use RuntimeException;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit_Framework_AssertionFailedError;
use Exception;

class MultipleTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->shrinker = new Multiple(
            [
                new IntegerGenerator()
            ],
            function ($number) {
                $this->assertLessThanOrEqual(5000, $number);
            }
        );
        $this->attempts = [];
        $this->shrinker->onAttempt(function ($attempt) {
            $this->attempts[] = $attempt;
        });
    }
    
    public function originallyFailedTests()
    {
        return [
            ['startingPoint' => 5500],
            ['startingPoint' => 6000],
            ['startingPoint' => 10000],
            ['startingPoint' => 100000],
        ];
    }
    
    /**
     * @dataProvider originallyFailedTests
     */
    public function testMultipleBranchesConvergeFasterThanLinearShrinking($startingPoint)
    {
        try {
            $this->shrinker->from(
                GeneratedValueSingle::fromValueAndInput(
                    [
                        $startingPoint
                    ],
                    [
                        GeneratedValueSingle::fromJustValue($startingPoint, 'integer')
                    ]
                ),
                new RuntimeException()
            );
        } catch (AssertionFailedError $e) {
            $this->verifyAssertionFailure($e, $startingPoint);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            $this->verifyAssertionFailure($e, $startingPoint);
        }
    }

    private function verifyAssertionFailure(Exception $e, $startingPoint)
    {
        $this->assertEquals("Failed asserting that 5001 is equal to 5000 or is less than 5000.", $e->getMessage());
        $allValues = array_map(function ($generatedValue) {
            return $generatedValue->unbox();
        }, $this->attempts);
        $linearShrinkingAttempts = $startingPoint - 5000;
        $this->assertLessThan(0.2 * $linearShrinkingAttempts, count($allValues));
    }
}
