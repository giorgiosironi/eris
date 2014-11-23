<?php
namespace Eris;
use SimpleXMLElement;

class ExampleEnd2EndTest extends \PHPUnit_Framework_TestCase
{
    public function testTheSampleAndSampleShrinkTestShouldBePassing()
    {
        $this->runExample('SampleTest.php');
        $this->assertAllTestsArePassing();
    }

    private function runExample($testFile)
    {
        $examplesDir = realpath(__DIR__ . '/../../examples');
        $samplesTestCase = $examplesDir . '/' . $testFile;
        $logFile = tempnam(sys_get_temp_dir(), 'phpunit_log_');
        $phpunitCommand = "vendor/bin/phpunit --log-junit $logFile $samplesTestCase";
        exec($phpunitCommand, $output, $returnCode);
        $this->assertSame(0, $returnCode);
        $this->results = new SimpleXMLElement(file_get_contents($logFile));
    }

    private function assertAllTestsArePassing()
    {
        $this->assertEquals(0, (string) $this->results->testsuite->attributes()['failures']);
        $this->assertEquals(0, (string) $this->results->testsuite->attributes()['errors']);
    }
}
