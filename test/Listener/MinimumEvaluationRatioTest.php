<?php
namespace Eris\Listener;

use Eris\Generator\GeneratedValue;

class MinimumEvaluationRatioTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->listener = new MinimumEvaluationRatio(0.5);
    }

    public function testAllowsExecutionsWithHigherThanMinimumRatioToBeGreen()
    {
        $this->listener->endPropertyVerification(99, 100); 
    }

    /**
     * @expectedException OutOfBoundsException 
     */
    public function testWarnsOfDangerouslyLowEvaluationRatiosAsVeryFewTestsAreBeingPerformed()
    {
        $this->listener->endPropertyVerification(20, 100); 
    }
}
