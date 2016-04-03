<?php
namespace Eris\Listener;

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
        $this->log->endPropertyVerification(null);
    }
    
    public function testWritesALineForEachIterationShowingItsIndex()
    {
        $this->log->newGeneration([], 42);
        $this->assertEquals(
            "[2011-03-13T07:06:40+00:00][1234] iteration 42" . PHP_EOL,
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
