<?php
namespace Eris\Listener;

use Eris\Generator\GeneratedValueSingle;

class LogTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->file = '/tmp/eris-log-unit-test.log';
        $this->time = function () {
            return 1300000000;
        };
        $this->log = new Log($this->file, $this->time, 1234);
    }

    public function tearDown()
    {
        $this->log->endPropertyVerification(null, null);
    }
    
    public function testWritesALineForEachIterationShowingItsIndex()
    {
        $this->log->newGeneration([23], 42);
        $this->assertEquals(
            "[2011-03-13T07:06:40+00:00][1234] iteration 42: [23]" . PHP_EOL,
            file_get_contents($this->file)
        );
    }

    public function testWritesALineForTheFirstFailureOfATest()
    {
        $this->log->failure([23], new \PHPUnit_Framework_AssertionFailedError("Failed asserting that..."));
        $this->assertEquals(
            "[2011-03-13T07:06:40+00:00][1234] failure: [23]. Failed asserting that..." . PHP_EOL,
            file_get_contents($this->file)
        );
    }

    public function testWritesALineForEachShrinkingAttempt()
    {
        $this->log->shrinking([22], new \PHPUnit_Framework_AssertionFailedError("Failed asserting that..."));
        $this->assertEquals(
            "[2011-03-13T07:06:40+00:00][1234] shrinking: [22]" . PHP_EOL,
            file_get_contents($this->file)
        );
    }

    public function testCleansTheFile()
    {
        $this->log->newGeneration([], 42);
        $this->log = new Log($this->file, $this->time, 1234);

        $this->assertEquals("", file_get_contents($this->file));
    }
}
