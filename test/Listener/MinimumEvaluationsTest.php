<?php
namespace Eris\Listener;

use LogicException;

class MinimumEvaluationsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var MinimumEvaluations
     */
    private $listener;

    protected function setUp(): void
    {
        $this->listener = MinimumEvaluations::ratio(0.5);
    }

    public function testAllowsExecutionsWithHigherThanMinimumRatioToBeGreen(): void
    {
        $this->assertNull($this->listener->endPropertyVerification(99, 100));
    }

    public function testWarnsOfDangerouslyLowEvaluationRatiosAsVeryFewTestsAreBeingPerformed(): void
    {
        $this->expectExceptionMessage("Evaluation ratio 0.2 is under the threshold 0.5");
        $this->expectException(\OutOfBoundsException::class);
        $this->listener->endPropertyVerification(20, 100);
    }

    public function testIfTheTestIsAlreadyFailingDoesNotCreateNoiseWithItsOwnCheck(): void
    {
        $this->assertNull(
            $this->listener->endPropertyVerification(10, 100, new LogicException("One of the cross beams has gone out askew on the treadle"))
        );
    }
}
