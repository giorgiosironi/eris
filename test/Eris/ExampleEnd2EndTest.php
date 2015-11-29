<?php
namespace Eris;
use SimpleXMLElement;

class ExampleEnd2EndTest extends \PHPUnit_Framework_TestCase
{
    private $testFile;
    private $testsByName;
    private $results;
    private $environment = [];

    public static function fullyGreenTestFiles()
    {
        return [
            ["BooleanTest.php"],
            ["CharacterTest.php"],
            ["ChooseTest.php"],
            ["ConstantTest.php"],
            ["ElementsTest.php"],
            ["GeneratorSamplesTest.php"],
            ["IntegerTest.php"],
            ["NamesTest.php"],
            ["RegexTest.php"],
            ["SampleTest.php"],
            ["SequenceTest.php"],
            ["SetTest.php"],
            ["SubsetTest.php"],
            ["SortTest.php"],
            ["TupleTest.php"],
            ["VectorTest.php"],
        ];
    }

    /**
     * @dataProvider fullyGreenTestFiles
     */
    public function testAllTestClassesWhichAreFullyGreen($testCaseFileName)
    {
        $this->runExample($testCaseFileName);
        $this->assertAllTestsArePassing();
    }

    public function testSequenceTest()
    {
        $this->runExample('SequenceTest.php');
        $this->assertAllTestsArePassing();
    }

    public function testCharacterTests()
    {
        $this->runExample('CharacterTest.php');
        $this->assertAllTestsArePassing();
    }

    public function testStringShrinkingTests()
    {
        $this->runExample('StringTest.php');
        $this->assertTestsAreFailing(1);
        $errorMessage = (string) $this->theTest('testLengthPreservation')->failure;
        $this->assertRegexp(
            "/Concatenating '' to '.{6}' gives '.{6}ERROR'/",
            $errorMessage,
            "It seems there is a problem with shrinking: we were expecting a minimal error message but instead the one for StringTest::testLengthPreservation() didn't match"
        );
    }

    public function testShrinkingAndAntecedentsTests()
    {
        $this->runExample('ShrinkingTest.php');
        $this->assertTestsAreFailing(1);
        $errorMessage = (string) $this->theTest('testShrinkingRespectsAntecedents')->failure;
        $this->assertRegexp(
            "/The number 11 is not multiple of 29/",
            $errorMessage,
            "It seems there is a problem with shrinking: we were expecting an error message containing '11' since it's the lowest value in the domain that satisfies the antecedents."
        );
    }

    public function testShrinkingTimeLimitTest()
    {
        $this->runExample('ShrinkingTimeLimitTest.php');
        $this->assertTestsAreFailing(1);
        $executionTime = (float) $this->theTest('testLengthPreservation')->attributes()['time'];
        $this->assertGreaterThanOrEqual(4.0, $executionTime);
        $this->assertLessThanOrEqual(13.0, $executionTime);
    }

    public function testGenericErrorTest()
    {
        $this->setEnvironmentVariable('ERIS_ORIGINAL_INPUT', 1);
        $this->runExample('ErrorTest.php');
        $this->assertTestsAreFailing(1);
        $errorMessage = (string) $this->theTest('testGenericExceptionsDoNotShrinkButStillShowTheInput')->error;
        $this->assertRegexp(
            "/Original input:/",
            $errorMessage
        );
    }

    public function testFloatTests()
    {
        $this->runExample('FloatTest.php');
        $this->assertTestsAreFailing(1);
    }

    public function testDateTest()
    {
        $this->runExample('DateTest.php');
        $this->assertTestsAreFailing(1);
    }

    public function testSumTests()
    {
        $this->runExample('SumTest.php');
        $this->assertTestsAreFailing(3);
    }

    public function testFrequencyTests()
    {
        $this->runExample('FrequencyTest.php');
        $this->assertTestsAreFailing(1);
        $this->assertRegexp(
            '/Failed asserting that 1 matches expected 0./',
            (string) $this->theTest('testAlwaysFails')->failure
        );
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

    private function setEnvironmentVariable($name, $value)
    {
        $this->environment[$name] = $value;
    }

    private function runExample($testFile)
    {
        $this->testFile = $testFile;
        $examplesDir = realpath(__DIR__ . '/../../examples');
        $samplesTestCase = $examplesDir . '/' . $testFile;
        $logFile = tempnam(sys_get_temp_dir(), 'phpunit_log_');
        $environmentVariables = [];
        foreach ($this->environment as $name => $value) {
            $environmentVariables[] .= "$name=$value";
        }
        $phpunitCommand = implode(" ", $environmentVariables) . " vendor/bin/phpunit --log-junit $logFile $samplesTestCase";
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
            "The test examples/{$this->testFile} was expected to have $number red tests, but instead has {$numberOfErrorsAndFailures}. Run it with `vendor/bin/phpunit examples/{$this->testFile} and find out why." . PHP_EOL
            . "Also, here is the dump of the test run we just performed:" . PHP_EOL
            . var_export($this->results, true)
        );
    }
}
