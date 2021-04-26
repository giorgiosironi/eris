<?php
namespace Eris;

class Facade
{
    use TestTrait {
        limitTo as public;
        minimumEvaluationRatio as public;
        shrinkingTimeLimit as public;
        withRand as public;
    }

    public function __construct()
    {
        $this->erisSetupBeforeClass();
        $this->erisSetup();
    }

    /**
     * sadly this facade has no option to retrieve annotations of testcases
     * @return array
     */
    protected function getTestCaseAnnotations()
    {
        return array();
    }
}
