<?php
namespace Eris;
use SimpleXMLElement;

class ExampleEnd2EndTest extends \PHPUnit_Framework_TestCase
{
    private $testFile;
    private $testsByName;
    private $results;

    public function testConstantGenerator()
    {
        $this->runExample('ConstantTest.php');
        $this->assertAllTestsArePassing();
    }

    public function testTheSampleAndSampleShrinkTestShouldBePassing()
    {
        $this->runExample('SampleTest.php');
        $this->assertAllTestsArePassing();
    }

    public function testVectorTests()
    {
        $this->runExample('VectorTest.php');
        $this->assertAllTestsArePassing();
    }

    public function testTupleTest()
    {
        $this->runExample('TupleTest.php');
        $this->assertAllTestsArePassing();
    }

    public function testStringTests()
    {
        $this->runExample('StringTest.php');
        $this->assertTestsAreFailing(1);
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

    public function testElementsTests()
    {
        $this->runExample('ElementsTest.php');
        $this->assertAllTestsArePassing();
    }

    public function testBooleanTests()
    {
        $this->runExample('BooleanTest.php');
        $this->assertAllTestsArePassing();
    }

    private function runExample($testFile)
    {
        $this->testFile = $testFile;
        $examplesDir = realpath(__DIR__ . '/../../examples');
        $samplesTestCase = $examplesDir . '/' . $testFile;
        $logFile = tempnam(sys_get_temp_dir(), 'phpunit_log_');
        $phpunitCommand = "vendor/bin/phpunit --log-junit $logFile $samplesTestCase";
        exec($phpunitCommand, $output);
        $contentsOfXmlLog = file_get_contents($logFile);
        if (!$contentsOfXmlLog) {
            $this->fail(
                "It appears the command" . PHP_EOL
                . $phpunitCommand . PHP_EOL
                . "has crashed without leaving a log for us to analyze." . PHP_EOL
                . "This was its output: " . implode(PHP_EOL, $output) . PHP_EOL
            );
        }
        $this->results = new SimpleXMLElement($contentsOfXmlLog);
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
        $numberOfErrorsAndFailures =
            (int)$this->results->testsuite['failures'] +
            (int)$this->results->testsuite['errors'];

        $this->assertSame(
            $number,
            $numberOfErrorsAndFailures,
            "The test examples/{$this->testFile} was expected to have $number red tests, but instead has {$numberOfErrorsAndFailures}. Run it with `vendor/bin/phpunit examples/{$this->testFile} and find out why"
        );
    }
}
