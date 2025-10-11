<?php
namespace Eris;

use PHPUnit\Framework\Attributes\DataProvider;
use SimpleXMLElement;

class ExampleEnd2EndTest extends \PHPUnit\Framework\TestCase
{
    private ?string $testFile = null;
    private $testsByName;
    private ?\SimpleXMLElement $results = null;
    private array $environment = [];

    public static function fullyGreenTestFiles(): array
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

    #[DataProvider('fullyGreenTestFiles')]
    public function testAllTestClassesWhichAreFullyGreen(string $testCaseFileName): void
    {
        $this->runExample($testCaseFileName);
        $this->assertNoTestsAreRed();
    }

    public function testSequenceTest(): void
    {
        $this->runExample('SequenceTest.php');
        $this->assertNoTestsAreRed();
    }

    public function testCharacterTests(): void
    {
        $this->runExample('CharacterTest.php');
        $this->assertNoTestsAreRed();
    }

    public function testStringShrinkingTests(): void
    {
        $this->runExample('StringTest.php');
        $this->assertTestsAreFailing(1);
        $errorMessage = (string) $this->theTest('testLengthPreservation')->failure;
        self::assertMatchesRegularExpression(
            "/Concatenating '' to '.{6}' gives '.{6}ERROR'/",
            $errorMessage,
            "It seems there is a problem with shrinking: we were expecting a minimal error message but instead the one for StringTest::testLengthPreservation() didn't match"
        );
    }

    public function testShrinkingAndAntecedentsTests(): void
    {
        $this->runExample('ShrinkingTest.php');
        $this->assertTestsAreFailing(2);
        self::assertMatchesRegularExpression(
            "/Failed asserting that .* does not contain \"B\"/",
            (string) $this->theTest('testShrinkingAString')->failure
        );
        self::assertMatchesRegularExpression(
            "/The number 11 is not multiple of 29/",
            (string) $this->theTest('testShrinkingRespectsAntecedents')->failure,
            "It seems there is a problem with shrinking: we were expecting an error message containing '11' since it's the lowest value in the domain that satisfies the antecedents."
        );
    }

    public function testShrinkingTimeLimitTest(): void
    {
        $this->runExample('ShrinkingTimeLimitTest.php');
        $this->assertTestsAreFailing(2);
        $executionTime = (float) $this->theTest('testLengthPreservation')->attributes()['time'];
        self::assertMatchesRegularExpression(
            '/Eris has reached the time limit for shrinking/',
            (string) $this->theTest('testLengthPreservation')->error,
            var_export($this->theTest('testLengthPreservation'), true)
        );
        // one failure, two shrinking attempts: 2.0 + 2.0 == 4.0 seconds, plus some overhead
        $this->assertLessThanOrEqual(9.0, $executionTime);
    }

    public function testDisableShrinkingTest(): void
    {
        $this->runExample('DisableShrinkingTest.php');
        $this->assertTestsAreFailing(1);
        self::assertMatchesRegularExpression(
            '/Total calls: 1\n/',
            (string) $this->theTest('testThenIsNotCalledMultipleTime')->failure
        );
    }

    public function testLimitToTest(): void
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

    public function testGenericErrorTest(): void
    {
        // TODO: turn on this by default? Or remove it?
        $this->setEnvironmentVariable('ERIS_ORIGINAL_INPUT', 1);
        $this->runExample('ErrorTest.php');
        $this->assertTestsAreFailing(1);
        $errorMessage = (string) $this->theTest('testGenericExceptionsDoNotShrinkButStillShowTheInput')->error;
        self::assertMatchesRegularExpression(
            "/Original input:/",
            $errorMessage
        );
    }

    public function testFloatTests(): void
    {
        $this->runExample('FloatTest.php');
        $this->assertTestsAreFailing(1);
    }

    public function testDateTest(): void
    {
        $this->runExample('DateTest.php');
        $this->assertTestsAreFailing(1);
    }

    public function testSumTests(): void
    {
        $this->runExample('SumTest.php');
        $this->assertTestsAreFailing(3);
    }

    public function testFrequencyTests(): void
    {
        $this->runExample('FrequencyTest.php');
        $this->assertTestsAreFailing(1);
        self::assertMatchesRegularExpression(
            '/Failed asserting that (1|100|200) matches expected 0./',
            (string) $this->theTest('testAlwaysFails')->failure
        );
    }

    public function testSuchThatTest(): void
    {
        $this->runExample('SuchThatTest.php');
        $this->assertTestsAreFailing(3);
        self::assertMatchesRegularExpression(
            '/number was asserted to be more than 100, but it\'s 43/',
            (string) $this->theTest('testSuchThatShrinkingRespectsTheCondition')->failure
        );
        self::assertMatchesRegularExpression(
            '/number was asserted to be more than 42, but it\'s 0/',
            (string) $this->theTest('testSuchThatAcceptsPHPUnitConstraints')->failure
        );
        self::assertMatchesRegularExpression(
            '/number was asserted to be more than 100, but it\'s 0/',
            (string) $this->theTest('testSuchThatShrinkingRespectsTheConditionButTriesToSkipOverTheNotAllowedSet')->failure
        );
    }

    public function testWhenTests(): void
    {
        $this->runExample('WhenTest.php');
        $this->assertTestsAreFailing(2);
        self::assertMatchesRegularExpression(
            "/should be less or equal to 100, but/",
            (string) $this->theTest('testWhenFailingWillNaturallyHaveALowEvaluationRatioSoWeDontWantThatErrorToObscureTheTrueOne')->failure
        );
        self::assertMatchesRegularExpression(
            "/Evaluation ratio .* is under the threshold/",
            (string) $this->theTest('testWhenWhichSkipsTooManyValues')->error
        );
    }

    public function testMapTest(): void
    {
        $this->runExample('MapTest.php');
        $this->assertTestsAreFailing(2);
        self::assertMatchesRegularExpression(
            "/number is not less than 100/",
            (string) $this->theTest('testShrinkingJustMappedValues')->failure
        );
        self::assertMatchesRegularExpression(
            "/triple sum array/",
            (string) $this->theTest('testShrinkingMappedValuesInsideOtherGenerators')->failure
        );
    }

    public function testLogFileTest(): void
    {
        $this->runExample('LogFileTest.php');
        $this->assertTestsAreFailing(1);
        self::assertMatchesRegularExpression(
            "/asserting that 43 is equal to 42 or is less than 42/",
            (string) $this->theTest('testLogOfFailuresAndShrinking')->failure
        );
    }

    public function testReproducibilityWithSeed(): void
    {
        $this->markTestSkipped();

        $this->runExample('AlwaysFailsTest.php');
        $result = $this->results->testsuite->testcase;
        $output = (string) $result->{"system-out"};
        if (!preg_match('/ERIS_SEED=(\d+)/', $output, $matches)) {
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

    public function testSizeCustomization(): void
    {
        $this->runExample('SizeTest.php');
        $this->assertTestsAreFailing(1);
        self::assertMatchesRegularExpression(
            "/Failed asserting that 100000 is less than 100000/",
            (string) $this->theTest('testMaxSizeCanBeIncreased')->failure
        );
    }

    public function testMinimumEvaluations(): void
    {
        $this->runExample('MinimumEvaluationsTest.php');
        $this->assertTestsAreFailing(1);
        self::assertMatchesRegularExpression(
            "/Evaluation ratio 0\..* is under the threshold 0\.5/",
            (string) $this->theTest('testFailsBecauseOfTheLowEvaluationRatio')->error
        );
    }

    public function testGeneratingIntegersWithAScript(): void
    {
        $output = $this->runScript('generating_integers.php');
        $this->assertEquals(100, count($output));
    }

    private function setEnvironmentVariable(string $name, int|string $value): void
    {
        $this->environment[$name] = $value;
    }

    private function runExample(string $testFile): void
    {
        $this->testFile = $testFile;
        $examplesDir = realpath(__DIR__ . '/../examples');
        $samplesTestCase = $examplesDir . DIRECTORY_SEPARATOR . $testFile;
        $logFile = tempnam(sys_get_temp_dir(), 'phpunit_log_');
        $environmentVariables = [];
        foreach ($this->environment as $name => $value) {
            $var = "$name=$value";
            $environmentVariables[] = DIRECTORY_SEPARATOR==='\\' ? "set $var && " : $var;
        }
        $bin = "vendor".DIRECTORY_SEPARATOR."bin".DIRECTORY_SEPARATOR."phpunit";
        $phpunitCommand = implode(" ", $environmentVariables) . " $bin --log-junit $logFile $samplesTestCase";
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

    private function runScript(string $filename)
    {
        $examplesDir = realpath(__DIR__ . '/../examples');
        $command = "php {$examplesDir}/{$filename}";
        exec($command, $outputLines, $exitCode);
        $this->assertEquals(0, $exitCode);
        return $outputLines;
    }

    private function theTest(string $name)
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

    private function assertNoTestsAreRed(): void
    {
        $this->assertTestsAreFailing(0);
    }

    private function assertTestsAreFailing(int $number): void
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
