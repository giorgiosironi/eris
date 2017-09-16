<?php
namespace Eris;

class Facade
{
    use TestTrait {
        limitTo as traitLimitTo;
        minimumEvaluationRatio as traitMinimumEvaluationRatio;
        shrinkingTimeLimit as traitShrinkingTimeLimit;
        withRand as traitWithRand;
    }

    public function __construct()
    {
        $this->erisSetupBeforeClass();
        $this->erisSetup();
    }

    public function limitTo($limit)
    {
        return $this->traitLimitTo($limit);
    }

    public function minimumEvaluationRatio($ratio)
    {
        return $this->traitMinimumEvaluationRatio($ratio);
    }

    public function shrinkingTimeLimit($shrinkingTimeLimit)
    {
        return $this->traitShrinkingTimeLimit($shrinkingTimeLimit);
    }

    public function withRand($randFunction)
    {
        return $this->traitWithRand($randFunction);
    }

}
