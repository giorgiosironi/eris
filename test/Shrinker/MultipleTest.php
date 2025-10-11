<?php
namespace Eris\Shrinker;

use Eris\Generator\GeneratedValueSingle;
use Eris\Generator\IntegerGenerator;
use Exception;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Attributes\DataProvider;
use RuntimeException;

class MultipleTest extends \PHPUnit\Framework\TestCase
{
    private \Eris\Shrinker\Multiple $shrinker;
    private array $attempts;

    public function setUp(): void
    {
        $this->shrinker = new Multiple(
            [
                new IntegerGenerator()
            ],
            function ($number): void {
                $this->assertLessThanOrEqual(5000, $number);
            }
        );
        $this->attempts = [];
        $this->shrinker->onAttempt(function ($attempt): void {
            $this->attempts[] = $attempt;
        });
    }
    
    public static function originallyFailedTests(): array
    {
        return [
            ['startingPoint' => 5500],
            ['startingPoint' => 6000],
            ['startingPoint' => 10000],
            ['startingPoint' => 100000],
        ];
    }

    #[DataProvider('originallyFailedTests')]
    public function testMultipleBranchesConvergeFasterThanLinearShrinking(int $startingPoint): void
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
        }
    }

    private function verifyAssertionFailure(Exception $e, int $startingPoint): void
    {
        $this->assertEquals("Failed asserting that 5001 is equal to 5000 or is less than 5000.", $e->getMessage());
        $allValues = array_map(fn ($generatedValue) => $generatedValue->unbox(), $this->attempts);
        $linearShrinkingAttempts = $startingPoint - 5000;
        $this->assertLessThan(0.2 * $linearShrinkingAttempts, count($allValues));
    }
}
