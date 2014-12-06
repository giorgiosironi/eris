<?php
namespace Eris;
use SimpleXMLElement;

class ExampleEnd2EndTest extends \PHPUnit_Framework_TestCase
{
    private $testsByName;
    private $results;
    private $returnCode;

    public function testTheSampleAndSampleShrinkTestShouldBePassing()
    {
        $this->runExample('SampleTest.php');
        $this->assertAllTestsArePassing();
    }

    public function testWhenTests()
    {
        $this->runExample('WhenTest.php');
        $this->assertTestsAreFailing(2);
        $this->assertRegexp(
            "/should be less or equal to 100, but/",
            (string) $this->theTest('testWhenFailingWillNaturallyHaveALowEvaluationRatioSoWeDontWantThatErrorToObscureTheTrueOne')->failure
        );
        $this->assertRegexp(
            "/Evaluation ratio .* is under the threshold/",
            (string) $this->theTest('testWhenWhichSkipsTooManyValues')->error
        );
    }

    private function runExample($testFile)
    {
        $examplesDir = realpath(__DIR__ . '/../../examples');
        $samplesTestCase = $examplesDir . '/' . $testFile;
        $logFile = tempnam(sys_get_temp_dir(), 'phpunit_log_');
        $phpunitCommand = "vendor/bin/phpunit --log-junit $logFile $samplesTestCase";
        exec($phpunitCommand, $output, $returnCode);
        $this->returnCode = $returnCode;
        $this->results = new SimpleXMLElement(file_get_contents($logFile));
    }

    private function theTest($name)
    {
        if ($this->testsByName === null) { 
            $this->testsByName = [];
            foreach ($this->results->testsuite->testcase as $testCase) {
                $testName = (string) $testCase->attributes()['name'];
                $this->testsByName[$testName] = $testCase;
            }
        }

        return $this->testsByName[$name];
    }

    private function assertAllTestsArePassing()
    {
        $this->assertTestsAreFailing(0);
    }

    private function assertTestsAreFailing($number)
    {
        $this->assertSame($number, $this->returnCode);
        $this->assertEquals(
            $number,
            ((string) $this->results->testsuite->attributes()['failures'])
            + ((string) $this->results->testsuite->attributes()['errors'])
        );
    }
}
