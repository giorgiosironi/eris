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
            ["AssociativeArrayTest.php"],
            ["BooleanTest.php"],
            ["CharacterTest.php"],
            ["ChooseTest.php"],
            ["CollectTest.php"],
            ["ConstantTest.php"],
            ["DifferentElementsTest.php"],
            ["ElementsTest.php"],
            ["GeneratorSamplesTest.php"],
            ["IntegerTest.php"],
            ["LimitToTest.php"],
            ["NamesTest.php"],
            ["OneOfTest.php"],
            ["RandConfigurationTest.php"],
            ["RegexTest.php"],
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
        $this->assertNoTestsAreRed();
    }

    public function testSequenceTest()
    {
        $this->runExample('SequenceTest.php');
        $this->assertNoTestsAreRed();
    }

    public function testCharacterTests()
    {
        $this->runExample('CharacterTest.php');
        $this->assertNoTestsAreRed();
    }

    public function testStringShrinkingTests()
    {
        $this->runExample('StringTest.php');
        $this->assertTestsAreFailing(1);
        $errorMessage = (string) $this->theTest('testLengthPreservation')->failure;
        $this->assertRegExp(
            "/Concatenating '' to '.{6}' gives '.{6}ERROR'/",
            $errorMessage,
            "It seems there is a problem with shrinking: we were expecting a minimal error message but instead the one for StringTest::testLengthPreservation() didn't match"
        );
    }

    public function testShrinkingAndAntecedentsTests()
    {
        $this->runExample('ShrinkingTest.php');
        $this->assertTestsAreFailing(2);
        $this->assertRegExp(
            "/Failed asserting that .* does not contain \"B\"/",
            (string) $this->theTest('testShrinkingAString')->failure
        );
        $this->assertRegExp(
            "/The number 11 is not multiple of 29/",
            (string) $this->theTest('testShrinkingRespectsAntecedents')->failure,
            "It seems there is a problem with shrinking: we were expecting an error message containing '11' since it's the lowest value in the domain that satisfies the antecedents."
        );
    }

    public function testShrinkingTimeLimitTest()
    {
        $this->runExample('ShrinkingTimeLimitTest.php');
        $this->assertTestsAreFailing(1);
        $executionTime = (float) $this->theTest('testLengthPreservation')->attributes()['time'];
        $this->assertRegexp(
            '/Eris has reached the time limit for shrinking/',
            (string) $this->theTest('testLengthPreservation')->error,
            var_export($this->theTest('testLengthPreservation'), true)
        );
        // one failure, two shrinking attempts: 2.0 + 2.0 == 4.0 seconds, plus some overhead
        $this->assertLessThanOrEqual(5.0, $executionTime);
    }

    public function testDisableShrinkingTest()
    {
        $this->runExample('DisableShrinkingTest.php');
        $this->assertTestsAreFailing(1);
        $this->assertRegExp(
            '/Total calls: 1\n/',
            (string) $this->theTest('testThenIsNotCalledMultipleTime')->failure
        );
    }

    public function testLimitToTest()
    {
        $this->runExample('LimitToTest.php');
        $this->assertTestsAreFailing(0);
        $this->assertEquals(
            5,
            (string) $this->theTest('testNumberOfIterationsCanBeConfigured')->attributes()['assertions'],
            "We configured a small number of iterations for this test, but a different number were performed"
        );
        $this->assertLessThan(
            100,
            (string) $this->theTest('testTimeIntervalToRunForCanBeConfiguredAndAVeryLowNumberOfIterationsCanBeIgnored')->attributes()['assertions'],
            "We configured a small time limit for this test, but still all iterations were performed"
        );
    }

    public function testGenericErrorTest()
    {
        // TODO: turn on this by default? Or remove it?
        $this->setEnvironmentVariable('ERIS_ORIGINAL_INPUT', 1);
        $this->runExample('ErrorTest.php');
        $this->assertTestsAreFailing(1);
        $errorMessage = (string) $this->theTest('testGenericExceptionsDoNotShrinkButStillShowTheInput')->error;
        $this->assertRegExp(
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
        $this->assertRegExp(
            '/Failed asserting that (1|100|200) matches expected 0./',
            (string) $this->theTest('testAlwaysFails')->failure
        );
    }

    public function testSuchThatTest()
    {
        $this->runExample('SuchThatTest.php');
        $this->assertTestsAreFailing(3);
        $this->assertRegexp(
            '/number was asserted to be more than 100, but it\'s 43/',
            (string) $this->theTest('testSuchThatShrinkingRespectsTheCondition')->failure
        );
        $this->assertRegexp(
            '/number was asserted to be more than 42, but it\'s 0/',
            (string) $this->theTest('testSuchThatAcceptsPHPUnitConstraints')->failure
        );
        $this->assertRegexp(
            '/number was asserted to be more than 100, but it\'s 0/',
            (string) $this->theTest('testSuchThatShrinkingRespectsTheConditionButTriesToSkipOverTheNotAllowedSet')->failure
        );
    }

    public function testWhenTests()
    {
        $this->runExample('WhenTest.php');
        $this->assertTestsAreFailing(2);
        $this->assertRegExp(
            "/should be less or equal to 100, but/",
            (string) $this->theTest('testWhenFailingWillNaturallyHaveALowEvaluationRatioSoWeDontWantThatErrorToObscureTheTrueOne')->failure
        );
        $this->assertRegExp(
            "/Evaluation ratio .* is under the threshold/",
            (string) $this->theTest('testWhenWhichSkipsTooManyValues')->error
        );
    }

    public function testMapTest()
    {
        $this->runExample('MapTest.php');
        $this->assertTestsAreFailing(2);
        $this->assertRegExp(
            "/number is not less than 100/",
            (string) $this->theTest('testShrinkingJustMappedValues')->failure
        );
        $this->assertRegExp(
            "/triple sum array/",
            (string) $this->theTest('testShrinkingMappedValuesInsideOtherGenerators')->failure
        );
    }

    public function testLogFileTest()
    {
        $this->runExample('LogFileTest.php');
        $this->assertTestsAreFailing(1);
        $this->assertRegexp(
            "/asserting that 43 is equal to 42 or is less than 42/",
            (string) $this->theTest('testLogOfFailuresAndShrinking')->failure
        );
    }

    public function testReproducibilityWithSeed()
    {
        $this->runExample('AlwaysFailsTest.php');
        $result = $this->results->testsuite->testcase;
        $output = (string) $result->{"system-out"};
        if (!preg_match('/ERIS_SEED=([0-9]+)/', $output, $matches)) {
            $this->fail("Cannot find ERIS_SEED in output to rerun the test deterministically: " . var_export($output, true));
        }
        $this->setEnvironmentVariable('ERIS_SEED', $matches[1]);
        $this->runExample('AlwaysFailsTest.php');
        $secondRunResult = $this->results->testsuite->testcase;
        $this->assertEquals(
            $result->failure,
            $secondRunResult->failure
        );
    }

    public function testSizeCustomization()
    {
        $this->runExample('SizeTest.php');
        $this->assertTestsAreFailing(1);
        $this->assertRegexp(
            "/Failed asserting that 100000 is less than 100000/",
            (string) $this->theTest('testMaxSizeCanBeIncreased')->failure
        );
    }

    public function testMinimumEvaluations()
    {
        $this->runExample('MinimumEvaluationsTest.php');
        $this->assertTestsAreFailing(1);
        $this->assertRegexp(
            "/Evaluation ratio 0\..* is under the threshold 0\.5/",
            (string) $this->theTest('testFailsBecauseOfTheLowEvaluationRatio')->error
        );
    }

    public function testGeneratingIntegersWithAScript()
    {
        $output = $this->runScript('generating_integers.php');
        $this->assertEquals(100, count($output));
    }

    private function setEnvironmentVariable($name, $value)
    {
        $this->environment[$name] = $value;
    }

    private function runExample($testFile)
    {
        $this->testFile = $testFile;
        $examplesDir = realpath(__DIR__ . '/../examples');
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

    private function runScript($filename)
    {
        $examplesDir = realpath(__DIR__ . '/../examples');
        $command = "php {$examplesDir}/{$filename}";
        exec($command, $outputLines, $exitCode);
        $this->assertEquals(0, $exitCode);
        return $outputLines;
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

    private function assertNoTestsAreRed()
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
