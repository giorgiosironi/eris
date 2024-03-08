<?php
namespace Eris;

use Throwable;

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
    protected function getTestCaseAttributes()
    {
        return array();
    }

    protected function toString(): string
    {
        return '';
    }

    protected function onNotSuccessfulTest(Throwable $t): never
    {
        throw $t;
    }
}
